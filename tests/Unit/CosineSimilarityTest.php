<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\StoreComplaintRequest;
use ReflectionClass;

class CosineSimilarityTest extends TestCase
{
    private function request(): StoreComplaintRequest
    {
        return new StoreComplaintRequest();
    }

    private function cosine(string $a, string $b): float
    {
        $r = new ReflectionClass(StoreComplaintRequest::class);
        $method = $r->getMethod('cosineSimilarity');
        $method->setAccessible(true);
        return $method->invoke($this->request(), $a, $b);
    }

    private function tokenize(string $text): array
    {
        $r = new ReflectionClass(StoreComplaintRequest::class);
        $method = $r->getMethod('tokenize');
        $method->setAccessible(true);
        return $method->invoke($this->request(), $text);
    }

    /** @test */
    public function identical_strings_return_1(): void
    {
        $this->assertEquals(1.0, $this->cosine('pothole on main street', 'pothole on main street'));
    }

    /** @test */
    public function completely_different_strings_return_0(): void
    {
        $this->assertEquals(0.0, $this->cosine('pothole main street', 'flooding river road'));
    }

    /** @test */
    public function similar_strings_return_high_score(): void
    {
        $score = $this->cosine('Large pothole on Main Street', 'Large pothole in Main Street');
        $this->assertGreaterThan(0.75, $score);
    }

    /** @test */
    public function empty_string_returns_0(): void
    {
        $this->assertEquals(0.0, $this->cosine('', 'pothole main street'));
        $this->assertEquals(0.0, $this->cosine('pothole main street', ''));
        $this->assertEquals(0.0, $this->cosine('', ''));
    }

    /** @test */
    public function case_insensitive_comparison(): void
    {
        $score = $this->cosine('POTHOLE MAIN STREET', 'pothole main street');
        $this->assertEqualsWithDelta(1.0, $score, 1e-9);
    }

    /** @test */
    public function special_characters_are_stripped(): void
    {
        $score = $this->cosine("pothole!! main street??", 'pothole main street');
        $this->assertEqualsWithDelta(1.0, $score, 1e-9);
    }

    /** @test */
    public function tokenize_lowercases_and_splits(): void
    {
        $tokens = $this->tokenize('Hello World TEST');
        $this->assertContains('hello', $tokens);
        $this->assertContains('world', $tokens);
        $this->assertContains('test', $tokens);
    }

    /** @test */
    public function tokenize_strips_special_chars(): void
    {
        $tokens = $this->tokenize("it's broken; fix it!");
        $this->assertNotContains("it's", $tokens);
        $this->assertContains('its', $tokens);
    }

    /** @test */
    public function tokenize_empty_string_returns_empty_array(): void
    {
        $this->assertEmpty($this->tokenize(''));
        $this->assertEmpty($this->tokenize('   '));
    }

    /** @test */
    public function partially_overlapping_strings_score_between_0_and_1(): void
    {
        $score = $this->cosine('broken streetlight oak avenue', 'broken lamp oak road');
        $this->assertGreaterThan(0.0, $score);
        $this->assertLessThan(1.0, $score);
    }
}
