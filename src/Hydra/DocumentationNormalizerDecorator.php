<?php

namespace App\Hydra;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DocumentationNormalizerDecorator implements NormalizerInterface
{
    public function __construct(
        private readonly NormalizerInterface $decorated,
        private readonly Security $security,
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = $this->decorated->normalize($object, $format, $context);

        if (null === $this->security->getUser() && is_array($data)) {
            if (array_key_exists('hydra:entrypoint', $data)) {
                unset($data['hydra:entrypoint']);
            }
            if (array_key_exists('hydra:supportedClass', $data)) {
                $data['hydra:supportedClass'] = [];
            }
            if (array_key_exists('supportedClass', $data)) {
                $data['supportedClass'] = [];
            }
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    /**
     * @param string|null $format
     */
    public function getSupportedTypes($format): array
    {
        return $this->decorated->getSupportedTypes($format);
    }
}
