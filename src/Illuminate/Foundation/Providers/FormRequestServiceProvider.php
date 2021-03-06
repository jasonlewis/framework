<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Request;

class FormRequestServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app['events']->listen('router.matched', function()
		{
			$this->app->resolvingAny(function($resolved, $app)
			{
				// If the resolved instance is an instance of the FormRequest object, we will go
				// ahead and initialize the request as well as set a few dependencies on this
				// request instance. We will then run the "validate" method on the request.
				if ($resolved instanceof FormRequest)
				{
					$this->initializeRequest($resolved, $app['request']);

					$resolved->setContainer($app)
                             ->setRoute($app['Illuminate\Routing\Route'])
                             ->setRedirector($app['Illuminate\Routing\Redirector'])
                             ->validate($app['Illuminate\Validation\Factory']);
				}
			});
		});
	}

	/**
	 * Initialize the form request with data from the given request.
	 *
	 * @param  \Illuminate\Foundation\Http\FormRequest  $form
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @return void
	 */
	protected function initializeRequest(FormRequest $form, Request $current)
	{
		$form->initialize(
			$current->query->all(), $current->request->all(), $current->attributes->all(),
			$current->cookies->all(), $current->files->all(), $current->server->all(), $current->getContent()
		);
	}

}