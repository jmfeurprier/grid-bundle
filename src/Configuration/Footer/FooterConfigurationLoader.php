<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Footer;

use Jmf\Grid\Configuration\Preset\PresetApplier;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\TemplateRendering\StringTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Webmozart\Assert\Assert;

readonly class FooterConfigurationLoader
{
    public function __construct(
        private PresetApplier $presetApplier,
    ) {
    }

    /**
     * @param array<string, mixed> $footerConfig
     *
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function load(array $footerConfig): FooterConfiguration
    {
        Assert::isMap($footerConfig);

        $footerConfiguration = new FooterConfiguration(
            $this->getAlign($footerConfig),
            $this->getTemplate($footerConfig),
            $this->getMerge($footerConfig),
            $this->getValue($footerConfig),
            $this->getPresetId($footerConfig),
        );

        return $this->presetApplier->apply($footerConfiguration);
    }

    /**
     * @param array<string, mixed> $footerConfig
     */
    private function getAlign(array $footerConfig): ?string
    {
        $align = $footerConfig['align'] ?? null;

        Assert::nullOrStringNotEmpty($align);

        return $align;
    }

    /**
     * @param array<string, mixed> $footerConfig
     */
    private function getTemplate(array $footerConfig): ?TemplateInterface
    {
        $template = $footerConfig['template'] ?? null;

        if (null === $template) {
            return null;
        }

        Assert::stringNotEmpty($template);

        // @todo
        return new StringTemplate($template);
    }

    /**
     * @param array<string, mixed> $footerConfig
     */
    private function getMerge(array $footerConfig): ?int
    {
        $merge = $footerConfig['merge'] ?? null;

        Assert::nullOrPositiveInteger($merge);

        return $merge;
    }

    /**
     * @param array<string, mixed> $footerConfig
     */
    private function getValue(array $footerConfig): ?string
    {
        $value = $footerConfig['value'] ?? null;

        Assert::nullOrString($value);

        return $value;
    }

    /**
     * @param array<string, mixed> $footerConfig
     *
     * @return null|non-empty-string
     */
    private function getPresetId(array $footerConfig): ?string
    {
        $presetId = $footerConfig['preset'] ?? null;

        Assert::nullOrStringNotEmpty($presetId);

        return $presetId;
    }
}
