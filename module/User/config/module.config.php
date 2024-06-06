<?php

declare(strict_types=1);

namespace User;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'admin_user' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/admin/user[/:action[/:id[/page[/:page]]]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'index',
                        'page' => 1,
                    ],
                ],
            ],
            'forgot' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/forgot',
                    'defaults' => [
                        'controller' => Controller\PasswordController::class,
                        'action' => 'forgot',
                    ],
                ],
            ],
            'reset' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/reset[/:id[/:token]]',
                    'constraints' => [
                        'id' => '[0-9]+',
                        'token' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\PasswordController::class,
                        'action' => 'reset',
                    ],
                ],
            ],
            'settings' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/settings[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SettingController::class,
                        'action' => 'index',
                    ],
                ], 
            ],
            'signup' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/signup',
                    'defaults' => [
                        'controller' => Controller\RegisterController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => Controller\LogoutController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'login' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/login[/:returnUrl]',
                    'constraints' => [
                        'returnUrl' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action' => 'index',
                    ]
                ]
            ],
            'profile' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/profile[/:username]',
                    'constraints' => [
                        'username' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action' => 'index',
                    ],
                ], 
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AdminController::class => Controller\SharedControllerFactory::class,
            Controller\LoginController::class => Controller\LoginControllerFactory::class,
            Controller\LogoutController::class => InvokableFactory::class,
            Controller\PasswordController::class => Controller\PasswordControllerFactory::class,
            Controller\ProfileController::class => Controller\SharedControllerFactory::class,
            Controller\RegisterController::class => Controller\SharedControllerFactory::class,
            Controller\SettingController::class => Controller\SharedControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            /** admin template map */
            'admin/index' => __DIR__ . '/../view/user/admin/index.phtml',
            /** login template map */
            'login/index' => __DIR__ . '/../view/user/login/index.phtml',
            /** password template map */
            'password/forgot' => __DIR__ . '/../view/user/password/forgot.phtml',
            'password/reset' => __DIR__ . '/../view/user/password/reset.phtml',
            /** profile template map */
            'profile/index' => __DIR__ . '/../view/user/profile/index.phtml',
            /** register template map */
            'register/index' => __DIR__ . '/../view/user/register/index.phtml',
            /** setting template map */
            'setting/avatar' => __DIR__ . '/../view/user/setting/avatar.phtml',
            'setting/delete' => __DIR__ . '/../view/user/setting/delete.phtml',
            'setting/email' => __DIR__ . '/../view/user/setting/email.phtml',
            'setting/index' => __DIR__ . '/../view/user/setting/index.phtml',
            'setting/password' => __DIR__ . '/../view/user/setting/password.phtml',
            'setting/username' => __DIR__ . '/../view/user/setting/username.phtml',
        ],
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
    ],
];
