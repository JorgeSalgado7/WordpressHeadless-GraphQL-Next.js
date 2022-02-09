<?php
    /**
     * WP Headless - Archivo de configuración
     *
     * Configuración básica para la creación de una plantilla WP Headless
     *
     * @link URL
     *
     * @package WordPress
     * @author Jorge Salgado
     * 
     * @version 1.0.0
     * 
     */

    
    if(!defined('REDIRECT_URL')){
        define('REDIRECT_URL', 'https://jorgesalgado.com.mx');
    }

    /**
     * Redirigimos todo el tráfico que intente acceder al front de WP
     * @see REDIRECT_URL
     */
    if(!function_exists('a_custom_redirect')){
        function a_custom_redirect(){
            header("Location: ". REDIRECT_URL);
            die();
        }
    }

    /**
     * Disponibilizamos el input "imagen destada" para la creación de posts
     * @see https://developer.wordpress.org/reference/functions/add_theme_support/
     */
    if(!function_exists('a_theme_setup')){
        function a_theme_setup(){
            add_theme_support('post-thumbnails');
        }
        add_action('adter_setup_theme', 'a_theme_setup');
    }

    /**
     * Permitimos cargar imágenes .svg al administrador
    */
    if(!function_exists('a_mime_types')){
        function a_mine_types($mimes){
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        }
        add_filter('upload_mimes', 'a_mime_types');
    }

    /**
     * Desabilitar editor de bloques
     * 
     * @see filtros debajo de está linea 
     * 
     * add_filter('use_block_editor_for_post', '__return_false', 10);
     * add_filter('use_block_editor_for_post_type', '__return_false', 10);
     * 
     */

    /**
     * Registramos la navegación para nuestro proyecto front
     * 
     * En este caso solo creamos 1 header y 1 footer, pero se pueden registrar más
     * 
     */
    if(!function_exists('a_custom_navigation_menus')){
        function a_custom_navigation_menus(){
            $locations = array(
                'header_wp_headless'    => __('Header WP Headless', 'wp-headless'),
                'footer_wp_headless'    => __('Footer WP Headless', 'wp-headless'),
            );
            register_nav_menus($locations);
        }
    }
    add_action('init', 'a_custom_navigation_menus');

    /**
     * Definimos posts personalizados
     * @see [documentación] https://developer.wordpress.org/reference/functions/register_post_type/
     * @see [iconos] https://developer.wordpress.org/resource/dashicons/#share
     */
    if(!function_exists('a_register_custom_post_types')){
        function a_register_custom_post_types(){

            $singular_name  = __('Ejemplo', 'wp-headless');
            $plural_name    = __('Ejemplos', 'wp-headless');
            $slug_name      = 'cpt-ejemplos';

            register_post_type($slug_name, array(
                'label'             => $singular_name,
                'public'            => true,
                'capability_type'   => 'post',
                'map_meta_cap'      => true,
                'has_archive'       => false,
                'query_var'         => $slug_name,
                //'supports'          => array('title', 'thumbnail', 'revisions'),
                'labels'            => a_get_custom_post_type_labels($singular_name, $plural_name),
                'menu_icon'         => 'dashicons-open-folder',
                'show_in_rest'      => true,
            ));

        }
        add_action('init', 'a_register_custom_post_types');
    }

    if(!function_exists('a_get_custom_post_type_labels')){
        function a_get_custom_post_type_labels($singular, $plural){
            $labels = array(
                'name'                  => $plural,
                'singular_name'         => $singular,
                'menu_name'             => $plural,
                'add_new'               => sprintf(__('Agregar %s', 'wp-headless'), $singular),
                'add_new_item'          => sprintf(__('Agregar new %s', 'wp-headless'), $singular),
                'edit'                  => __('Editar', 'wp-headless'),
                'edit_item'             => sprintf(__('Editar %s', 'wp-headless'), $singular),
                'new_item'              => sprintf(__('Nuevo %s', 'wp-headless'), $singular),
                'view'                  => sprintf(__('Ver %s', 'wp-headless'), $singular),
                'view_item'             => sprintf(__('Ver %s', 'wp-headless'), $singular),
                'search_items'          => sprintf(__('Buscar %s', 'wp-headless'), $plural),
                'not_found'             => sprintf(__('%s no encontrado', 'wp-headless'), $plural),
                'not_found_in_trash'    => sprintf(__('%s no encontrado en la papelera', 'wp-headless'), $plural),
                'parent'                => sprintf(__('%s padre', 'wp-headless'), $singular),
            );
            return $labels;
        }
    }
