<?php

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Controller\Taxon;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\ShopApiPlugin\Factory\Taxon\TaxonViewFactoryInterface;
use Sylius\ShopApiPlugin\Http\RequestBasedLocaleProviderInterface;
use Sylius\ShopApiPlugin\View\Taxon\TaxonView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

final class ShowTaxonTreeAction
{
    /** @var TaxonRepositoryInterface */
    private $taxonRepository;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var TaxonViewFactoryInterface */
    private $taxonViewFactory;

    /** @var RequestBasedLocaleProviderInterface */
    private $requestBasedLocaleProvider;

    public function __construct(
        TaxonRepositoryInterface $taxonRepository,
        ViewHandlerInterface $viewHandler,
        TaxonViewFactoryInterface $taxonViewFactory,
        RequestBasedLocaleProviderInterface $requestBasedLocaleProvider
    ) {
        $this->taxonRepository = $taxonRepository;
        $this->viewHandler = $viewHandler;
        $this->taxonViewFactory = $taxonViewFactory;
        $this->requestBasedLocaleProvider = $requestBasedLocaleProvider;
    }

    /**
     * Show taxon tree.
     *
     * This endpoint will return an array of all available taxon roots with all of its children.
     *
     * @SWG\Tag(name="Taxons")
     * @SWG\Parameter(
     *     name="locale",
     *     in="query",
     *     type="string",
     *     description="Locale in which products should be shown.",
     *     required=false
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Array of all available taxons.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Sylius\ShopApiPlugin\View\Taxon\TaxonView::class))
     *     )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $localeCode = $this->requestBasedLocaleProvider->getLocaleCode($request);

        $taxons = $this->taxonRepository->findRootNodes();
        $taxonViews = [];

        /** @var TaxonInterface $taxon */
        foreach ($taxons as $taxon) {
            $taxonViews[] = $this->buildTaxonView($taxon, $localeCode);
        }

        return $this->viewHandler->handle(View::create($taxonViews, Response::HTTP_OK));
    }

    private function buildTaxonView(TaxonInterface $taxon, string $localeCode): TaxonView
    {
        $taxonView = $this->taxonViewFactory->create($taxon, $localeCode);

        foreach ($taxon->getChildren() as $childTaxon) {
            $taxonView->children[] = $this->buildTaxonView($childTaxon, $localeCode);
        }

        return $taxonView;
    }
}
