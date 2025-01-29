<?php

/**
 * Global function for rendering CSS asset includes, by default if it's production
 * it will include the asset with https otherwise with http
 *
 * @param string $paths
 * @param bool $secure
 * @return string
 */
if (!function_exists('inject_css')) {
    function inject_css($paths, $secure = null)
    {
        if(is_array($paths)) {
            $include = '';
            foreach ($paths as $path) {
                $include .= inject_css($path,$secure);
            }
            return $include;
        }
        //$secure = ($secure) ?? (env('APP_ENV') == 'production') ? true : false;
        return '<link rel="stylesheet" href="' . asset('css/'.$paths.'.css?v=' . config('app.version'), $secure) . '"></script>';
    }
}

/**
 * Global function for rendering JavaScript asset includes, by default if it's production
 * it will include the asset with https otherwise with http
 *
 * @param string $path
 * @param bool $secure
 * @return string
 */
if (!function_exists('inject_js')) {
    function inject_js($paths, $secure = null)
    {
        if(is_array($paths)) {
            $include = '';
            foreach ($paths as $path) {
                $include .= inject_js($path,$secure);
            }
            return $include;
        }
        //$secure = ($secure) ?? (env('APP_ENV') == 'production') ? true : false;
        return '<script src="' . asset('js/'.$paths.'.js?v=' . config('app.version'), $secure) . '"></script>';
    }
}

/**
 * Global function for rendering icons
 *
 * @param string $icon
 * @param string $class
 * @return string
 */
if (!function_exists('icon')) {
    function icon($icon,$class = '')
    {
        return '<i class="fas fa-' . $icon . ' ' . $class . '"></i>';
    }
}

/**
 * Global function for fetching logged in user
 *
 * @return \App\Models\User
 */
if (!function_exists('user')) {
    function user()
    {
        return auth()->user();
    }
}

/**
 * Global function for fetching focused project
 *
 * @return \App\Models\Project
 */
if (!function_exists('project')) {
    function project()
    {
        $user = user();
        return $user ? $user->focusedProject() : null;
    }
}
