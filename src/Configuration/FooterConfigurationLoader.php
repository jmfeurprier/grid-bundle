<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration;

use Jmf\Grid\Preset\PresetApplier;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\TemplateRendering\StringTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Webmozart\Assert\Assert;

class FooterConfigurationLoader
{
    /**
     * @var array<string, mixed>
     */
    private array $footerConfig;

    public function __construct(
        private readonly PresetApplier $presetApplier,
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

        $this->footerConfig = $footerConfig;

        $footerConfiguration = new FooterConfiguration(
            $this->getAlign(),
            $this->getTemplate(),
            $this->getMerge(),
            $this->getValue(),
            $this->getPresetId(),
        );

        return $this->presetApplier->apply($footerConfiguration);
    }

    private function getAlign(): ?string
    {
        $align = $this->footerConfig['align'] ?? null;

        Assert::nullOrString($align);

        return $align;
    }

    private function getTemplate(): ?TemplateInterface
    {
        $template = $this->footerConfig['template'] ?? null;

        if (null === $template) {
            return null;
        }

        Assert::stringNotEmpty($template);

        // @todo
        return new StringTemplate($template);
    }

    private function getMerge(): ?int
    {
        $merge = $this->footerConfig['merge'] ?? null;

        Assert::nullOrPositiveInteger($merge);

        return $merge;
    }

    private function getValue(): ?string
    {
        $value = $this->footerConfig['value'] ?? null;

        Assert::nullOrString($value);

        return $value;
    }

    /**
     * @return null|non-empty-string
     */
    private function getPresetId(): ?string
    {
        $presetId = $this->footerConfig['preset'] ?? null;

        Assert::nullOrStringNotEmpty($presetId);

        return $presetId;
    }
}
