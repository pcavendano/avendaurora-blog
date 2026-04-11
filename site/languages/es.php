<?php

return [
    'code' => 'es',
    'default' => true,
    'name' => 'Español',
    'locale' => 'es_MX.utf-8',
    'url' => '/',
    'direction' => 'ltr',
    'translations' => [
        // Navigation
        'nav.home' => 'Inicio',
        'nav.recipes' => 'Recetas',
        'nav.mi_cocina' => 'Mi Cocina',
        'nav.ingredients' => 'Ingredientes',
        'nav.stores' => 'Tiendas',
        'nav.about' => 'Aurora',
        'nav.contact' => 'Contacto',
        'nav.search' => 'Buscar',

        // Home page
        'home.from_my_kitchen' => 'Desde Mi Cocina',

        // Recipe categories
        'category.antojitos' => 'Antojitos y Botanas',
        'category.platos-fuertes' => 'Platos Fuertes',
        'category.sopas-caldos' => 'Sopas y Caldos',
        'category.salsas' => 'Salsas y Aderezos',
        'category.mariscos' => 'Mariscos',
        'category.desayunos' => 'Desayunos',
        'category.postres' => 'Postres',
        'category.bebidas' => 'Bebidas',
        'category.vegetarianos' => 'Vegetarianos',

        // Recipe details
        'recipe.prep_time' => 'Tiempo de Preparación',
        'recipe.cook_time' => 'Tiempo de Cocción',
        'recipe.total_time' => 'Tiempo Total',
        'recipe.servings' => 'Porciones',
        'recipe.difficulty' => 'Dificultad',
        'recipe.difficulty.easy' => 'Fácil',
        'recipe.difficulty.medium' => 'Medio',
        'recipe.difficulty.hard' => 'Difícil',
        'recipe.ingredients' => 'Ingredientes',
        'recipe.instructions' => 'Instrucciones',
        'recipe.tips' => 'Tips del Chef',
        'recipe.history' => 'Historia',
        'recipe.related' => 'Recetas Relacionadas',
        'recipe.save' => 'Guardar Receta',
        'recipe.print' => 'Imprimir',
        'recipe.share' => 'Compartir',

        // Ingredient kit
        'kit.title' => 'Kit de Ingredientes',
        'kit.available_at' => 'Disponible en',
        'kit.buy_kit' => 'Comprar Kit',
        'kit.add_to_cart' => 'Agregar al Carrito',
        'kit.customize' => 'Personalizar Kit',
        'kit.missing' => 'Faltantes',
        'kit.total' => 'Total del Kit',

        // Ingredients encyclopedia
        'ingredient.heat_level' => 'Nivel de Picor',
        'ingredient.scoville' => 'Escala Scoville',
        'ingredient.forms' => 'Formas Disponibles',
        'ingredient.uses' => 'Usos Culinarios',
        'ingredient.storage' => 'Almacenamiento',
        'ingredient.substitutes' => 'Sustitutos',
        'ingredient.related' => 'Ingredientes Relacionados',
        'ingredient.did_you_know' => '¿Sabías que...?',
        'ingredient.where_to_buy' => 'Dónde Comprar',
        'ingredient.recipes_with' => 'Recetas con este Ingrediente',

        // Forms
        'form.fresh' => 'Fresco',
        'form.dried' => 'Seco',
        'form.canned' => 'Enlatado',
        'form.frozen' => 'Congelado',
        'form.powder' => 'En Polvo',
        'form.paste' => 'En Pasta',
        'form.pickled' => 'En Escabeche',

        // General
        'general.view_all' => 'Ver Todos',
        'general.read_more' => 'Leer Más',
        'general.back' => 'Volver',
        'general.search_placeholder' => 'Buscar recetas, ingredientes...',
        'general.no_results' => 'No se encontraron resultados',
        'search.result' => 'resultado',
        'search.results' => 'resultados',
        'search.for' => 'para',

        // Account
        'account.sign_in' => 'Iniciar Sesión',
        'account.sign_out' => 'Cerrar Sesión',
        'account.create_account' => 'Crear Cuenta',
        'account.my_profile' => 'Mi Perfil',
        'account.have_account' => '¿Ya tienes cuenta?',
        'account.have_account_desc' => 'Inicia sesión para acceder a tus recetas favoritas.',
        'account.no_account' => '¿No tienes cuenta?',
        'account.no_account_desc' => 'Crea una cuenta gratis y guarda tus recetas favoritas.',
        'account.field_name' => 'Nombre',
        'account.field_email' => 'Email',
        'account.field_password' => 'Contraseña',
        'account.field_password_confirm' => 'Confirmar Contraseña',
        'account.field_language' => 'Idioma Preferido',
        'account.password_help' => 'Mínimo 8 caracteres',
        'account.newsletter' => 'Suscribirme al newsletter',
        'account.settings' => 'Configuración',
        'account.save' => 'Guardar Cambios',
        'account.saved' => 'Cambios guardados',
        'account.favorites' => 'Mis Favoritas',
        'account.no_favorites' => 'Aún no has guardado ninguna receta.',
        'account.browse_recipes' => 'Explorar Recetas',
        'account.error_csrf' => 'Token de seguridad inválido. Recarga la página.',
        'account.error_required' => 'Email y contraseña son obligatorios.',
        'account.error_invalid_email' => 'El email no es válido.',
        'account.error_password_short' => 'La contraseña debe tener al menos 8 caracteres.',
        'account.error_password_mismatch' => 'Las contraseñas no coinciden.',
        'account.error_email_taken' => 'Ya existe una cuenta con este email.',
        'account.error_invalid_credentials' => 'Email o contraseña incorrectos.',

        // Recipe favorites
        'recipe.favorite' => 'Favorito',
        'recipe.unfavorite' => 'Quitar',
        'general.no_stores' => 'No hay tiendas disponibles',
        'general.loading' => 'Cargando...',

        // Filters
        'filter.all' => 'Todos',

        // Ingredient categories
        'ingredient.category.chiles' => 'Chiles',
        'ingredient.category.especias' => 'Especias',
        'ingredient.category.hierbas' => 'Hierbas',
        'ingredient.category.granos' => 'Granos y Semillas',
        'ingredient.category.lacteos' => 'Lácteos',
        'ingredient.category.carnes' => 'Carnes',
        'ingredient.category.otros' => 'Otros',

        // Stores
        'store.delivery' => 'Envío a Domicilio',
        'store.delivery_available' => 'Envío Disponible',
        'store.about' => 'Acerca de',
        'store.specialties' => 'Especialidades',
        'store.contact' => 'Contacto',
        'store.hours' => 'Horarios',
        'store.visit_website' => 'Visitar Sitio Web',
        'store.shop_online' => 'Comprar en Línea',
        'store.ingredients_available' => 'Ingredientes Disponibles',

        // Footer
        'footer.about' => 'Sobre Nosotros',
        'footer.contact' => 'Contacto',
        'footer.privacy' => 'Privacidad',
        'footer.terms' => 'Términos',
        'footer.newsletter' => 'Suscríbete a nuestro boletín',
        'footer.newsletter_placeholder' => 'Tu correo electrónico',
        'footer.subscribe' => 'Suscribirse',
        'footer.copyright' => '© 2026 Avenda Aurora. Todos los derechos reservados.',
    ]
];
