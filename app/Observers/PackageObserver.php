<?php

namespace App\Observers;

use App\Models\Package;
use Illuminate\Support\Str;

class PackageObserver
{
    /**
     * Handle the Package "creating" event.
     */
    public function creating(Package $package): void
    {
        $package->slug = $this->generateUniqueSlug(
            $package->getTranslation("name", app()->getLocale() ?? "en")
        );
    }

    /**
     * Handle the Package "updating" event.
     */
    public function updating(Package $package): void
    {
        if ($package->isDirty("name")) {
            $package->slug = $this->generateUniqueSlug(
                $package->getTranslation("name", app()->getLocale() ?? "en"),
                $package->id
            );
        }
    }

    /**
     * Generate a unique slug for the package.
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
        $query = Package::where("slug", $slug);
        if ($excludeId) {
            $query->where("id", "!=", $excludeId);
        }
        return $query->exists();
    }
}
