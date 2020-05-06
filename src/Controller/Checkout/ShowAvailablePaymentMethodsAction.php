<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Checkout;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\ShopApiPlugin\Factory\Checkout\PaymentMethodViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Swagger\Annotations as SWG;

final class ShowAvailablePaymentMethodsAction
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var PaymentMethodsResolverInterface */
    private $paymentMethodsResolver;

    /** @var PaymentMethodViewFactoryInterface */
    private $paymentMethodViewFactory;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        ViewHandlerInterface $viewHandler,
        PaymentMethodsResolverInterface $paymentMethodResolver,
        PaymentMethodViewFactoryInterface $paymentMethodViewFactory,
        FactoryInterface $stateMachineFactory
    ) {
        $this->cartRepository = $cartRepository;
        $this->viewHandler = $viewHandler;
        $this->paymentMethodsResolver = $paymentMethodResolver;
        $this->paymentMethodViewFactory = $paymentMethodViewFactory;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * Get available payment methods.
     *
     * This endpoint will show you available payment methods for an order.
     *
     * @SWG\Tag(name="Checkout")
     * @SWG\Parameter(
     *     name="token",
     *     in="path",
     *     type="string",
     *     description="Cart identifier.",
     *     required=true
     * )
     * @SWG\Response(
     *     response=204,
     *     description="List of available payment methods."
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
        /** @var OrderInterface|null $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $request->attributes->get('token')]);

        if (null === $cart) {
            throw new NotFoundHttpException('Cart with given token does not exist!');
        }

        if (!$this->isCheckoutTransitionPossible($cart, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT)) {
            throw new BadRequestHttpException('The payment methods cannot be resolved in the current state of cart!');
        }

        $payments = [];
        foreach ($cart->getPayments() as $payment) {
            $payments['payments'][] = $this->getPaymentMethods($payment, $cart->getLocaleCode());
        }

        return $this->viewHandler->handle(View::create($payments));
    }

    private function getPaymentMethods(PaymentInterface $payment, string $locale): array
    {
        $rawPaymentMethods = [];

        /** @var PaymentMethodInterface $paymentMethod */
        foreach ($this->paymentMethodsResolver->getSupportedMethods($payment) as $paymentMethod) {
            $rawPaymentMethods['methods'][$paymentMethod->getCode()] = $this->paymentMethodViewFactory->create($paymentMethod, $locale);
        }

        return $rawPaymentMethods;
    }

    private function isCheckoutTransitionPossible(OrderInterface $cart, string $transition): bool
    {
        return $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->can($transition);
    }
}
