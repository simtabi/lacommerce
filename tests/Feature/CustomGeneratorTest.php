<?php

declare(strict_types=1);

namespace Simtabi\Lacommerce\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Simtabi\Lacommerce\Generators\Contracts\SkuGeneratorInterface;
use Simtabi\Lacommerce\Generators\Contracts\TicketNumberGeneratorInterface;
use Simtabi\Lacommerce\Tests\Fixtures\Generators\FallbackSkuGenerator;
use Simtabi\Lacommerce\Tests\Fixtures\Generators\SlugTicketNumberGenerator;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyProduct;
use Simtabi\Lacommerce\Tests\Fixtures\Models\DummyTicket;
use Simtabi\Lacommerce\Tests\TestCase;

/**
 * The two extension seams docs/tools/generators.md documents, configured the way it says to configure them.
 */
final class CustomGeneratorTest extends TestCase
{
    protected function publishedConfig(): array
    {
        $config = self::defaultConfig();
        $config['generator']['sku']['generator']           = FallbackSkuGenerator::class;
        $config['generator']['ticket_number']['generator'] = SlugTicketNumberGenerator::class;

        return ['simtabi.lacommerce' => $config];
    }

    #[Test]
    public function the_container_resolves_the_configured_generators(): void
    {
        $product = new DummyProduct(['name' => 'Blue shirt']);
        $ticket  = new DummyTicket(['name' => 'Printer jam']);

        $this->assertInstanceOf(FallbackSkuGenerator::class, resolve(SkuGeneratorInterface::class, ['model' => $product]));
        $this->assertInstanceOf(SlugTicketNumberGenerator::class, resolve(TicketNumberGeneratorInterface::class, ['model' => $ticket]));
    }

    #[Test]
    public function a_generator_extending_the_base_keeps_the_shipped_format_for_a_normal_source(): void
    {
        $product = DummyProduct::create(['name' => 'Blue shirt']);

        $this->assertMatchesRegularExpression('/^BLU-\d{10}$/', $product->sku);
    }

    #[Test]
    public function a_generator_extending_the_base_applies_its_own_source_logic(): void
    {
        $product = DummyProduct::create(['name' => '']);

        $this->assertMatchesRegularExpression('/^ITE-\d{10}$/', $product->sku);
    }

    #[Test]
    public function a_generator_extending_the_base_still_regenerates_on_update(): void
    {
        $product = DummyProduct::create(['name' => '']);

        $product->update(['name' => 'Red hat']);

        $this->assertMatchesRegularExpression('/^RED-\d{10}$/', $product->fresh()->sku);
    }

    #[Test]
    public function a_generator_implementing_only_the_interface_is_used_through_render(): void
    {
        // SlugTicketNumberGenerator has render() and no __toString(): the observer used to cast the
        // generator to a string, which threw for any generator that only implemented the interface.
        $this->assertFalse(method_exists(SlugTicketNumberGenerator::class, '__toString'));

        $ticket = DummyTicket::create(['name' => 'Printer jam']);

        $this->assertSame('TKT-PRINTER-JAM', $ticket->ticket_number);
    }

    #[Test]
    public function the_documented_example_is_this_fixture(): void
    {
        $docs    = (string) file_get_contents(__DIR__ . '/../../docs/tools/generators.md');
        $fixture = (string) file_get_contents(__DIR__ . '/../Fixtures/Generators/FallbackSkuGenerator.php');

        $example = str_replace(
            "namespace Simtabi\\Lacommerce\\Tests\\Fixtures\\Generators;",
            "namespace App\\Generators;",
            trim(preg_replace('/^<\?php\s+declare\(strict_types=1\);\s+/', '', $fixture)),
        );

        $this->assertStringContainsString($example, $docs, 'docs/tools/generators.md no longer shows tests/Fixtures/Generators/FallbackSkuGenerator.php');
    }
}
