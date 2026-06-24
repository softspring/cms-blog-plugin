<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Tests\Unit\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\RuntimeReflectionService;
use PHPUnit\Framework\TestCase;
use Softspring\CmsBlogPlugin\Entity\ArticleContent;
use Softspring\CmsBlogPlugin\EventListener\OverrideDoctrineClassSuperclassListener;

class OverrideDoctrineClassSuperclassListenerTest extends TestCase
{
    public function testItDoesNothingWithoutSuperclassList(): void
    {
        $metadata = $this->createMetadata();

        (new OverrideDoctrineClassSuperclassListener(null))->loadClassMetadata($this->createEventArgs($metadata));

        self::assertFalse($metadata->isMappedSuperclass);
    }

    public function testItMarksConfiguredClassesAsMappedSuperclass(): void
    {
        $metadata = $this->createMetadata();

        (new OverrideDoctrineClassSuperclassListener([ArticleContent::class]))->loadClassMetadata($this->createEventArgs($metadata));

        self::assertTrue($metadata->isMappedSuperclass);
    }

    private function createEventArgs(ClassMetadata $metadata): LoadClassMetadataEventArgs
    {
        $eventArgs = $this->createStub(LoadClassMetadataEventArgs::class);
        $eventArgs->method('getClassMetadata')->willReturn($metadata);

        return $eventArgs;
    }

    private function createMetadata(): ClassMetadata
    {
        $metadata = new ClassMetadata(ArticleContent::class);
        $metadata->initializeReflection(new RuntimeReflectionService());

        return $metadata;
    }
}
