<?php

namespace Tests\Unit;

use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Filament\Forms\Components\RichEditor;
use Tests\TestCase;

class RichEditorCuratorIntegrationTest extends TestCase
{
    public function test_rich_editors_use_curator_for_images(): void
    {
        $editor = RichEditor::make('content');

        $this->assertFalse($editor->hasFileAttachments());
        $this->assertContains('attachCuratorMedia', $editor->getToolbarButtons()[4]);
        $this->assertFalse($editor->hasToolbarButton('attachFiles'));
        $this->assertTrue($editor->hasToolbarButton('attachCuratorMedia'));
        $this->assertContainsOnlyInstancesOf(AttachCuratorMediaPlugin::class, $editor->getPlugins());
        $this->assertSame(config('curator.max_size'), $editor->getFileAttachmentsMaxSize());
    }
}
