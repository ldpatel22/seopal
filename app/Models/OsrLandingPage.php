<?php

namespace App\Models;

use App\Models\Traits\BelongsToReport;
use Illuminate\Database\Eloquent\Model;

class OsrLandingPage extends Model
{
    use BelongsToReport;

    protected $table = 'reports_osr_landing_pages';
    public $timestamps = false;

    protected $fillable = ['report_id','domain_id','url'];
    //protected $casts = ['data' => 'array'];

    public function domain() { return $this->belongsTo(OsrDomain::class,'domain_id'); }

    /**
     * Returns URL name stripped from domain
     *
     * @return string
     */
    public function name()
    {
        $name = explode($this->domain->name,$this->url)[1];

        if($name == '/' || $name == "") {
            return $this->domain->name;
        }

        return '...' . $name;
    }

    // TODO below must suffer due to data no longer being considered an array

    /**
     * Checks if landing page has a preview image
     *
     * @return bool
     */
    public function hasPreviewImage()
    {
        return isset($this->data['previewImage']) && $this->data['previewImage'] !== null;
    }

    /**
     * Returns preview image URL
     *
     * @return mixed
     */
    public function getPreviewImageUrl()
    {
        return $this->data['previewImage'];
    }

    /**
     * Returns preview image URL
     *
     * @return mixed
     */
    public function getFaviconUrl()
    {
        return 'http://www.google.com/s2/favicons?domain=' . $this->domain->name;
    }
}
