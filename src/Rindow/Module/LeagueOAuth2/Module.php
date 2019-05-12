<?php
namespace Rindow\Module\LeagueOAuth2;

class Module
{
    public function getConfig()
    {
        return array(
            'container' => array(
                'aliases' => array(
                //
                //    Incjects your OAuth2 client provider
                //        examples
                //           'League\\OAuth2\\Client\\Provider\\Google'
                //           'League\\OAuth2\\Client\\Provider\\Facebook'
                //           etc.
                //
                //    'Rindow\\Module\\LeagueOAuth2\\DefaultClientProvider' => 'your_client_provider',
                ),

                'components' => array(
                    'Rindow\\Module\\LeagueOAuth2\\DefaultOauth2Handler' => array(
                        'class' => 'Rindow\\Module\\LeagueOAuth2\\Handler\\Oauth2Handler',
                        'properties' => array(
                            'config' => array('config'=>'leagueOAuth2::handlers::default'),
                            'serviceLocator' => array('ref'=>'ServiceLocator'),
                            'store' => array('ref'=>'Rindow\\Module\\LeagueOAuth2\\DefaultOauth2HandlerStore'),
                        ),
                    ),
                    'Rindow\\Module\\LeagueOAuth2\\DefaultOauth2HandlerStore' => array(
                        'class' => 'Rindow\\Web\\Session\\Container',
                        'factory' => 'Rindow\\Web\\Session\\Container::factory',
                        'factory_args' => array(
                            'session' => 'Rindow\\Web\\Session\\DefaultSession',
                        ),
                    ),
                    'Rindow\\Module\\LeagueOAuth2\\DefaultGoogleProvider' => array(
                        'class' => 'Rindow\\Module\\LeagueOAuth2\\Client\\Provider\\GoogleEx',
                        'constructor_args' => array(
                            'options' => array('config'=>'leagueOAuth2::providers::default_google::options'),
                        ),
                    ),
                    'Rindow\\Module\\LeagueOAuth2\\DefaultFacebookProvider' => array(
                        'class' => 'League\\OAuth2\\Client\\Provider\\Facebook',
                        'constructor_args' => array(
                            'options' => array('config'=>'leagueOAuth2::providers::default_facebook::options'),
                        ),
                    ),
                    'Rindow\\Module\\LeagueOAuth2\\DefaultAuth0Provider' => array(
                        'class' => 'Rindow\\Module\\LeagueOAuth2\\Client\\Provider\\Auth0',
                        'constructor_args' => array(
                            'options' => array('config'=>'leagueOAuth2::providers::default_auth0::options'),
                        ),
                    ),
                ),
            ),

            'leagueOAuth2' => array(
                'handlers' => array(
                    'default' => array(
                        'providers' => array(
                            'google' => 'Rindow\\Module\\LeagueOAuth2\\DefaultGoogleProvider',
                            'facebook' => 'Rindow\\Module\\LeagueOAuth2\\DefaultFacebookProvider',
                            'auth0' => 'Rindow\\Module\\LeagueOAuth2\\DefaultAuth0Provider',
                        ),
                    ),
                ),
                'providers' => array(
                    'default_google' => array(
                        'options' => array(
                            //'clientId'     => '{google-app-id}',
                            //'clientSecret' => '{google-app-secret}',
                            //'redirectUri'  => 'http://your/app/url',
                            //'hostedDomain' => 'http://your-app-domain',
                            //'scopes'       => ['openid','email','profile'],
                        ),
                    ),
                    'default_facebook' => array(
                        'options' => array(
                            //'clientId'          => '{facebook-app-id}',
                            //'clientSecret'      => '{facebook-app-secret}',
                            //'redirectUri'       => 'http://your/app/url',
                            //'graphApiVersion'   => 'v2.8',
                        ),
                    ),
                    'default_auth0' => array(
                        'options' => array(
                            //'domain'       => '{auth0-domain}',
                            //'clientId'     => '{auth0-clientid}',
                            //'clientSecret' => '{auth0-clientsecret}',
                            //'audience'     => '{auth0-audience}', // When you want the JWT.
                            //'redirectUri'  => 'http://your/app/url',
                            //'scopes'       => ['openid','email','profile'],
                        ),
                    ),
                ),
            ),
        );
    }
}
