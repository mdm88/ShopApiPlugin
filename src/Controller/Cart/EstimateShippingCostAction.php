<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\ShopApiPlugin\Factory\Cart\EstimatedShippingCostViewFactoryInterface;
use Sylius\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Sylius\ShopApiPlugin\Request\Cart\EstimateShippingCostRequest;
use Sylius\ShopApiPlugin\Shipping\ShippingCostEstimatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

final class EstimateShippingCostAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ShippingCostEstimatorInterface */
    private $shippingCostEstimator;

    /** @var ValidatorInterface */
    private $validator;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var EstimatedShippingCostViewFactoryInterface */
    private $estimatedShippingCostViewFactory;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ShippingCostEstimatorInterface $shippingCostEstimator,
        ValidatorInterface $validator,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        EstimatedShippingCostViewFactoryInterface $estimatedShippingCostViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->shippingCostEstimator = $shippingCostEstimator;
        $this->validator = $validator;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->estimatedShippingCostViewFactory = $estimatedShippingCostViewFactory;
    }

    /**
     * Estimates the shipping cost of the cart.
     *
     * This endpoint will Estimates the shipping cost of the cart.
     *
     * @SWG\Tag(name="Cart")
     * @SWG\Parameter(
     *     name="token",
     *     in="path",
     *     type="string",
     *     description="Cart identifier.",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="countryCode",
     *     in="query",
     *     type="string",
     *     description="Shipping Country.",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="provinceCode",
     *     in="query",
     *     type="string",
     *     description="Shipping Province.",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Price was calculated.",
     *     @Model(type=Sylius\ShopApiPlugin\View\Cart\EstimatedShippingCostView::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid input, validation failed.",
     *     @Model(type=Sylius\ShopApiPlugin\View\ValidationErrorView::class)
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $estimateShippingCostRequest = new EstimateShippingCostRequest($request);

        $validationResults = $this->validator->validate($estimateShippingCostRequest);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(
                View::create(
                    $this->validationErrorViewFactory->create($validationResults),
                    Response::HTTP_BAD_REQUEST
                )
            );
        }

        $shippingCost = $this->shippingCostEstimator->estimate(
            $estimateShippingCostRequest->cartToken(),
            $estimateShippingCostRequest->countryCode(),
            $estimateShippingCostRequest->provinceCode()
        );

        return $this->viewHandler->handle(
            View::create($this->estimatedShippingCostViewFactory->create($shippingCost), Response::HTTP_OK)
        );
    }
}
