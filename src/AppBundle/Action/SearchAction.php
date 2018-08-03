<?php

namespace AppBundle\Action;

use Sidus\FilterBundle\Registry\QueryHandlerRegistry;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Basic search action
 */
class SearchAction
{
    /** @var EngineInterface */
    protected $renderEngine;

    /** @var QueryHandlerRegistry */
    protected $queryHandlerRegistry;

    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var RouterInterface */
    protected $router;

    /**
     * @param EngineInterface      $renderEngine
     * @param QueryHandlerRegistry $queryHandlerRegistry
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface      $router
     */
    public function __construct(
        EngineInterface $renderEngine,
        QueryHandlerRegistry $queryHandlerRegistry,
        FormFactoryInterface $formFactory,
        RouterInterface $router
    ) {
        $this->renderEngine = $renderEngine;
        $this->queryHandlerRegistry = $queryHandlerRegistry;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        // Create form with filters
        $builder = $this->formFactory->createBuilder(
            FormType::class,
            null,
            [
                'method' => 'get',
                'csrf_protection' => false,
                'action' => $this->router->generate(self::class),
                'validation_groups' => ['filters'],
            ]
        );

        $queryHandler = $this->queryHandlerRegistry->getQueryHandler('news');
        $form = $queryHandler->buildForm($builder);
        $queryHandler->handleRequest($request);

        return $this->renderEngine->renderResponse(
            'Search/action.html.twig',
            [
                'form' => $form->createView(),
                'results' => $queryHandler->getPager(),
            ]
        );
    }
}
