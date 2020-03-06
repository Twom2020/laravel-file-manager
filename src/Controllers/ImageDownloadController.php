<?php
/**
 * Created by SERJIK
 */

namespace Twom\FileManager\Controllers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Twom\FileManager\Models\File;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageDownloadController extends DownloadController
{
    /**
     * get image help by size
     *
     * @param $size
     * @param $file
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function image($size, $file)
    {
        /** @var File $file */
        $file = File::query()
            ->where("id", $file)
            ->orWhere("name", $file)
            ->firstOrFail();

        return redirect('/' . $file->base_path . "{$size}/" . $file->file_name);
    }
}
