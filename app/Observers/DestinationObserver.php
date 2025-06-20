<?php

namespace App\Observers;

use App\Models\Destination;
use Illuminate\Support\Str;

class DestinationObserver
{
    /**
     * Handle the Destination "creating" event.
     */
    public function creating(Destination $destination): void
    {
        $destination->slug = $this->generateUniqueSlug(
            $destination->getTranslation("name", app()->getLocale() ?? "en")
        );
    }

    /**
     * Handle the Destination "updating" event.
     */
    public function updating(Destination $destination): void
    {
        if ($destination->isDirty("name")) {
            $destination->slug = $this->generateUniqueSlug(
                $destination->getTranslation(
                    "name",
                    app()->getLocale() ?? "en"
                ),
                $destination->id
            );
        }
    }

    /**
     * Generate a unique slug for the destination.
     *
     * @param string $name
     * @param int|null $excludeId
     * @return string
     */
    protected function generateUniqueSlug(
        string $name,
        ?int $excludeId = null
    ): string {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . "-" . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists in the database.
     *
     * @param string $slug
     * @param int|null $excludeId
     * @return bool
     */
    protected function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Destination::where("slug", $slug);
        if ($excludeId) {
            $query->where("id", "!=", $excludeId);
        }
        return $query->exists();
    }
}
