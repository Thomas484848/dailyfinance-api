<?php

namespace App\Hydra;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class EntrypointNormalizerDecorator implements NormalizerInterface
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
            foreach (array_keys($data) as $key) {
                if (!in_array($key, ['@context', '@id', '@type'], true)) {
                    unset($data[$key]);
                }
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
