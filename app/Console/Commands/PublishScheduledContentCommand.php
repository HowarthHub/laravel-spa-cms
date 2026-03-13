<?php

namespace App\Console\Commands;

use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\ServiceModel;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class PublishScheduledContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled pages, posts, and services whose publish date has passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = 0;

        $count += $this->publishScheduled(PageModel::class);
        $count += $this->publishScheduled(PostModel::class);
        $count += $this->publishScheduled(ServiceModel::class);

        $this->info("Published {$count} item(s).");

        return Command::SUCCESS;
    }

    /**
     * Publish all scheduled items for the given model class.
     *
     * @param  class-string<PageModel|PostModel|ServiceModel>  $modelClass
     */
    private function publishScheduled(string $modelClass): int
    {
        /** @var Collection<int, PageModel|PostModel|ServiceModel> $items */
        $items = $modelClass::query()
            ->where('status', 'scheduled')
            ->where('published_at', '<=', now())
            ->get();

        $items->each(function (PageModel|PostModel|ServiceModel $item): void {
            $item->update(['status' => 'published']);
        });

        return $items->count();
    }
}
