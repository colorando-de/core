<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Configuration\Content\ContentType;
use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Bolt\Utils\ContentHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends TwigAwareController implements FrontendZoneInterface, DetailControllerInterface
{
    /** @var ContentRepository */
    private $contentRepository;

    /** @var ContentHelper */
    private $contentHelper;

    public function __construct(ContentRepository $contentRepository, ContentHelper $contentHelper)
    {
        $this->contentRepository = $contentRepository;
        $this->contentHelper = $contentHelper;
    }

    /**
     * @Route(
     *     "/{contentTypeSlug}/{slugOrId}",
     *     name="record",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%"},
     *     methods={"GET|POST"})
     * @Route(
     *     "/{_locale}/{contentTypeSlug}/{slugOrId}",
     *     name="record_locale",
     *     requirements={"contentTypeSlug"="%bolt.requirement.contenttypes%", "_locale": "%app_locales%"},
     *     methods={"GET|POST"})
     *
     * @param string|int $slugOrId
     */
    public function record($slugOrId, ?string $contentTypeSlug = null, bool $requirePublished = true): Response
    {
        // @todo should we check content type?
        if (is_numeric($slugOrId)) {
            $record = $this->contentRepository->findOneBy(['id' => (int) $slugOrId]);
        } else {
            $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));
            $record = $this->contentRepository->findOneBySlug($slugOrId, $contentType);
        }

        $this->contentHelper->setCanonicalPath($record);

        return $this->renderSingle($record, $requirePublished);
    }

    public function contentByFieldValue(string $contentTypeSlug, string $field, string $value): Response
    {
        $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));
        $record = $this->contentRepository->findOneByFieldValue($field, $value, $contentType);

        $this->contentHelper->setCanonicalPath($record);

        return $this->renderSingle($record);
    }
}
