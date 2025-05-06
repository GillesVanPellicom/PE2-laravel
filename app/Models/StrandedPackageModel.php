<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrandedPackageModel extends Model {

  protected $table = 'stranded_packages';

  protected $fillable = [
    'package_id',
    'is_resolved',
  ];

  /**
   * Mark the stranded package as resolved.
   *
   * @return void
   */
  public function resolve(): void {
    $this->is_resolved = true;
    $this->save();
  }

  /**
   * Check if the stranded package is resolved.
   *
   * @return bool
   */
  public function isResolved(): bool {
    return $this->is_resolved;
  }

  /**
   * Scope to get only unresolved stranded packages.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeUnresolved($query) {
    return $query->where('is_resolved', false);
  }
}