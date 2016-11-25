<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Usuarios\Controller\Index' => 'Usuarios\Controller\IndexController',
            'Usuarios\Controller\Usuario' => 'Usuarios\Controller\UsuarioController',
            'Usuarios\Controller\Login' => 'Usuarios\Controller\LoginController',
            'Usuarios\Controller\Alerta' => 'Usuarios\Controller\AlertaController',
            'Usuarios\Controller\Mensaje' => 'Usuarios\Controller\MensajeController',
            'Usuarios\Controller\Pagina' => 'Usuarios\Controller\PaginaController',
            'Usuarios\Controller\Input' => 'Usuarios\Controller\Inputs\InputController',
            'Usuarios\Controller\Pregunta' => 'Usuarios\Controller\PreguntaController',
            'Usuarios\Controller\Respuesta' => 'Usuarios\Controller\RespuestaController',
            'Usuarios\Controller\Formulario' => 'Usuarios\Controller\FormularioController',
            'Usuarios\Controller\FormularioEdit' => 'Usuarios\Controller\FormularioEditController',
            'Usuarios\Controller\Publicar' => 'Usuarios\Controller\PublicarController',
            'Usuarios\Controller\Categoria' => 'Usuarios\Controller\CategoriaController',
            'Usuarios\Controller\Articulo' => 'Usuarios\Controller\ArticuloController',
        ),
    ),
    'ml' => array(
        //Obligatorios
        'appId' => '6356096167782827',
        'key' => 'zxhrJWmdmsguvW6hh1HbLnP7WAWN69Wf',
        'authUrl' => 'http://localhost/index.php/usuarios/index/mlAuth/',
        //'listing_type' => 'free',
        'listing_type' => 'bronze',
        'store_id' => '',
        //'listing_type' => 'gold_premium',

        //Opcionales
        'urlServidorImagenes' => '',
        'urlServidorImagenes' => 'https://s3.amazonaws.com/melifarmashop/',
        'extensionImagenes' => 'jpg'
    ),
    'db' => array(
        'driver' =>'Pdo',
        'dsn' =>'mysql:dbname=ml;host=localhost',
        'username' => 'root',
        'password' => '',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Index',
                'route' => 'usuarios',
                'pages' => array(
                    'Users' => array(
                        'label' => 'Users',
                        'route' => 'usuarios',
                        'controller'=>'usuario',
                        'action' => 'index',
                        'pages' => array(
                            'list' => array(
                                'label' => 'List',
                                'route' => 'usuarios',
                                'controller'=>'usuario',
                                'action' => 'list'
                            ),
                            'add' => array(
                                'label' => 'Add',
                                'route' => 'usuarios',
                                'controller'=>'usuario',
                                'action' => 'add',
                            )
                        ),
                    ),
                ),
            ),
        ),
        'agent' => array(
            array(
                'label' => 'Index',
                'route' => 'usuarios',
                'pages' => array(
                    'Users' => array(
                        'label' => 'Users',
                        'route' => 'usuarios',
                        'controller'=>'index',
                        'action' => 'index'
                    ),
                    'Evaluations' => array(
                        'label' => 'Evaluations',
                        'route' => 'usuarios',
                        'controller'=>'evaluacion',
                        'action' => 'list',
                        'pages' => array(
                            'list' => array(
                                'label' => 'List',
                                'route' => 'usuarios',
                                'controller'=>'evaluacion',
                                'action' => 'list'
                            ),
                        ),
                    ),

                ),
            ),
        ),
    ),
    'service_manager'=>array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        )
    ),
    'router' => array(
        'routes' => array(
            'usuarios' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/usuarios[/:controller[/:action[/:id]]][/:param1][/:param2]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Usuarios\Controller',
                        'controller' => 'Usuarios\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action[/:id]]][/:param1][/:param2]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+'
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'formulario-edit/popup'  => __DIR__ . '/../view/usuarios/formulario-edit/popup.phtml',
        ),
        'template_path_stack' => array(
            'Usuarios' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
