<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/stocks' => [[['_route' => '_api_/stocks_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Stock', '_api_operation_name' => '_api_/stocks_get_collection', '_format' => null], null, ['GET' => 0], null, false, false, null]],
        '/api/user' => [
            [['_route' => '_api_/user_get', '_controller' => 'App\\Controller\\UserController', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/user_get', '_format' => null], null, ['GET' => 0], null, false, false, null],
            [['_route' => '_api_/user_patch', '_controller' => 'App\\Controller\\UserController', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/user_patch', '_format' => null], null, ['PATCH' => 0], null, false, false, null],
        ],
        '/api/me/password' => [[['_route' => 'api_me_password', '_controller' => 'App\\Controller\\AccountController::changePassword'], null, ['POST' => 0], null, false, false, null]],
        '/api/password-reset/request' => [[['_route' => 'api_password_reset_request', '_controller' => 'App\\Controller\\PasswordResetController::requestReset'], null, ['POST' => 0], null, false, false, null]],
        '/api/password-reset/confirm' => [[['_route' => 'api_password_reset_confirm', '_controller' => 'App\\Controller\\PasswordResetController::confirmReset'], null, ['POST' => 0], null, false, false, null]],
        '/api/register' => [[['_route' => 'api_register', '_controller' => 'App\\Controller\\RegistrationController::register'], null, ['POST' => 0], null, false, false, null]],
        '/' => [[['_route' => 'root_redirect', '_controller' => 'App\\Controller\\RootRedirectController'], null, ['GET' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'api_login', '_controller' => 'App\\Controller\\SecurityController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/watchlists' => [
            [['_route' => 'api_watchlists_list', '_controller' => 'App\\Controller\\WatchlistController::list'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_watchlists_create', '_controller' => 'App\\Controller\\WatchlistController::create'], null, ['POST' => 0], null, false, false, null],
        ],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api(?'
                    .'|/(?'
                        .'|docs(?:\\.([^/]++))?(*:37)'
                        .'|\\.well\\-known/genid/([^/]++)(*:72)'
                        .'|validation_errors/([^/]++)(*:105)'
                    .')'
                    .'|(?:/(index)(?:\\.([^/]++))?)?(*:142)'
                    .'|/(?'
                        .'|contexts/([^.]+)(?:\\.(jsonld))?(*:185)'
                        .'|errors/(\\d+)(?:\\.([^/]++))?(*:220)'
                        .'|validation_errors/([^/]++)(?'
                            .'|(*:257)'
                        .')'
                        .'|stocks/(?'
                            .'|([^/\\.]++)(?:\\.([^/]++))?(*:301)'
                            .'|([^/]++)/history(*:325)'
                        .')'
                        .'|watchlists/([^/]++)(?'
                            .'|(*:356)'
                            .'|/items(?'
                                .'|(*:373)'
                                .'|/([^/]++)(?'
                                    .'|(*:393)'
                                .')'
                            .')'
                        .')'
                    .')'
                .')'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:434)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        37 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => null, '_api_respond' => true], ['_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        72 => [[['_route' => 'api_genid', '_controller' => 'api_platform.action.not_exposed', '_api_respond' => true], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        105 => [[['_route' => 'api_validation_errors', '_controller' => 'api_platform.action.not_exposed'], ['id'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        142 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => null, '_api_respond' => true, 'index' => 'index'], ['index', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        185 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => true], ['shortName', '_format'], ['GET' => 0, 'HEAD' => 1], null, false, true, null]],
        220 => [[['_route' => '_api_errors', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\State\\ApiResource\\Error', '_api_operation_name' => '_api_errors', '_format' => null], ['status', '_format'], ['GET' => 0], null, false, true, null]],
        257 => [
            [['_route' => '_api_validation_errors_problem', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_problem', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_hydra', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_hydra', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_jsonapi', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_jsonapi', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_validation_errors_xml', '_controller' => 'api_platform.symfony.main_controller', '_stateless' => null, '_api_resource_class' => 'ApiPlatform\\Validator\\Exception\\ValidationException', '_api_operation_name' => '_api_validation_errors_xml', '_format' => null], ['id'], ['GET' => 0], null, false, true, null],
        ],
        301 => [[['_route' => '_api_/stocks/{id}{._format}_get', '_controller' => 'api_platform.action.not_exposed', '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Stock', '_api_operation_name' => '_api_/stocks/{id}{._format}_get', '_format' => null], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        325 => [[['_route' => 'api_stocks_history', '_controller' => 'App\\Controller\\StockHistoryController::history'], ['id'], ['GET' => 0], null, false, false, null]],
        356 => [
            [['_route' => 'api_watchlists_show', '_controller' => 'App\\Controller\\WatchlistController::show'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_watchlists_update', '_controller' => 'App\\Controller\\WatchlistController::update'], ['id'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'api_watchlists_delete', '_controller' => 'App\\Controller\\WatchlistController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        373 => [
            [['_route' => 'api_watchlist_items_list', '_controller' => 'App\\Controller\\WatchlistItemController::list'], ['watchlistId'], ['GET' => 0], null, false, false, null],
            [['_route' => 'api_watchlist_items_create', '_controller' => 'App\\Controller\\WatchlistItemController::create'], ['watchlistId'], ['POST' => 0], null, false, false, null],
        ],
        393 => [
            [['_route' => 'api_watchlist_items_update', '_controller' => 'App\\Controller\\WatchlistItemController::update'], ['watchlistId', 'itemId'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'api_watchlist_items_delete', '_controller' => 'App\\Controller\\WatchlistItemController::delete'], ['watchlistId', 'itemId'], ['DELETE' => 0], null, false, true, null],
        ],
        434 => [
            [['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
