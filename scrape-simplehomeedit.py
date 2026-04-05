import time
import requests
from bs4 import BeautifulSoup
from pymongo import MongoClient
from datetime import datetime
from pathlib import Path
import pandas as pd

BASE_URL = "https://simplehomeedit.com"
MONGO_URI = "mongodb://localhost:27017/"
DB_NAME = "recipes_db"
COLLECTION_NAME = "recipes"

client = MongoClient(MONGO_URI)
db = client[DB_NAME]
collection = db[COLLECTION_NAME]

session = requests.Session()
session.headers.update({"User-Agent": "MyRecipeScraper/1.0"})

print("MongoDB connecté!")

existing_urls = set()
csv_file = "recipes_simplehomeedit.csv"
if Path(csv_file).exists():
    existing_urls = set(pd.read_csv(csv_file)["url"].dropna())

print(f"{len(existing_urls)} URLs connues")

all_hrefs = []
page_num = 1
max_pages = 100

while page_num <= max_pages:
    url = f"{BASE_URL}/recipes/page/{page_num}/"
    print(f"Page {page_num}")
    resp = session.get(url, timeout=10)

    if resp.status_code == 404:
        break

    soup = BeautifulSoup(resp.text, "html.parser")

    if page_num == 1:
        pagination = soup.find('div', class_='bde-posts-pagination')
        if pagination:
            last_lis = pagination.find_all('li')
            if last_lis:
                last_li = last_lis[-1]
                last_a = last_li.find('a', href=True)
                if last_a:
                    last_page_match = last_a['href'].split('/page/')[-1].rstrip('/')
                    if last_page_match.isdigit():
                        max_pages = int(last_page_match)
                        print(f"Max pages: {max_pages}")
        max_pages = 3

    loop_items = soup.find_all(class_="bde-loop-item")
    if not loop_items:
        break

    for item in loop_items:
        link = item.find(class_="bde-container-link")
        if link and link.get("href"):
            all_hrefs.append(link["href"])

    page_num += 1
    time.sleep(1.5)

print(f"{len(all_hrefs)} liens trouvés")

new_count = 0
for i, href in enumerate(all_hrefs, 1):
    url = BASE_URL + href if href.startswith("/") else href

    if url in existing_urls:
        continue

    print(f"Recette {i}/{len(all_hrefs)}: {url}")
    resp = session.get(url, timeout=10)
    soup = BeautifulSoup(resp.text, "html.parser")

    recipe = {
        "url": url,
        "title": soup.select_one('h1.bde-heading').get_text(strip=True) if soup.select_one('h1.bde-heading') else '',
        "category": soup.select_one('.tasty-recipes-category a').get_text(strip=True) if soup.select_one(
            '.tasty-recipes-category a') else '',
        "description": soup.select_one('.post-content p').get_text(strip=True) if soup.select_one(
            '.post-content p') else '',
        "prep_time": soup.select_one('.tasty-recipes-prep-time').get_text(strip=True) if soup.select_one(
            '.tasty-recipes-prep-time') else '',
        "cook_time": soup.select_one('.tasty-recipes-cook-time').get_text(strip=True) if soup.select_one(
            '.tasty-recipes-cook-time') else '',
        "total_time": soup.select_one('.tasty-recipes-total-time').get_text(strip=True) if soup.select_one(
            '.tasty-recipes-total-time') else '',
        "servings": soup.select_one('.tasty-recipes-yield').get_text(strip=True) if soup.select_one(
            '.tasty-recipes-yield') else '',
        "ingredients": ' - '.join(
            [span.get_text(strip=True) for span in soup.select('.tasty-recipes-ingredients span')]),
        "instructions": ' - '.join(
            [span.get_text(strip=True) for span in soup.select('.tasty-recipes-instructions span')]),
        "original_author": soup.select_one('.tasty-recipes-author-name').get_text(strip=True) if soup.select_one(
            '.tasty-recipes-author-name') else '',
        "original_image": soup.select_one('.size-large img').get('src') if soup.select_one('.size-large img') else '',
        "scraped_date": datetime.now()
    }

    result = collection.replace_one(
        {"url": url},
        recipe,
        upsert=True
    )

    if result.upserted_id:
        new_count += 1
        print(f"Nouvelle: {recipe['title'][:40]}...")

    time.sleep(2)

print(f"{new_count} nouvelles recettes! Total DB: {collection.count_documents({})}")
client.close()
