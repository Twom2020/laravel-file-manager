<?php
/**
 * Created by Twomyan
 */

namespace Twom\FileManager\Types;

use Twom\FileManager\BaseType;
use Twom\FileManager\Models\File;
use Illuminate\Support\Facades\File as FileFacade;

class Image extends BaseType
{
    protected $sizes = null;

    protected $thumb = null;


    /**
     * @inheritDoc
     */
    protected function handle($file)
    {
        $path = $this->getFullPath();
        $originalPath = $path . "original/";

        if ($file->move($originalPath, $this->getFileName())) {
            $this->createFileRow();
        }

        $this->resize($originalPath . $this->getFileName(), $path, $this->getFileName());

        return $this;
    }


    protected function handleDelete(File $file)
    {
        if (is_null($this->getSizes())) {
            if ($sizes = $this->getConfig("sizes"))
                $this->setSizes($sizes);
            else
                $this->setSizes(["16", "24", "32", "64", "128"]);
        }

        if (is_null($this->getThumbSize())) {
            if (!$thumb = $this->getConfig("thumb"))
                $this->setThumbSize($thumb);
            else
                $this->setThumbSize("128");
        }

        $sizes = $this->getSizes();
        foreach ($sizes as $size) {
            $sizePath = $file->base_path . "{$size}/";
            $sizePath = $sizePath . $file->file_name;
            if ($file->private) {
                $sizePath = storage_path($sizePath);
            } else {
                $sizePath = public_path($sizePath);
            }

            FileFacade::delete($sizePath);
        }

        $thumbSize = $file->base_path . "thumb/" . $file->file_name;
        $originalSize = $file->base_path . "original/" . $file->file_name;

        if ($file->private) {
            $thumbSize = storage_path($thumbSize);
        } else {
            $thumbSize = public_path($thumbSize);
        }

        if ($file->private) {
            $originalSize = storage_path($originalSize);
        } else {
            $originalSize = public_path($originalSize);
        }

        FileFacade::delete($thumbSize);
        FileFacade::delete($originalSize);

        return true;
    }


    /**
     * resize image and return specific array of images
     *
     * @param $filePath
     * @param $uploadPath
     * @param $fileName
     * @return mixed
     */
    protected function resize($filePath, $uploadPath, $fileName)
    {
        if (is_null($this->getSizes())) {
            if ($sizes = $this->getConfig("sizes"))
                $this->setSizes($sizes);
            else
                $this->setSizes(["16", "24", "32", "64", "128"]);
        }

        if (is_null($this->getThumbSize())) {
            if (!$thumb = $this->getConfig("thumb"))
                $this->setThumbSize($thumb);
            else
                $this->setThumbSize("128");
        }

        $sizes = $this->getSizes();
        foreach ($sizes as $size) {
            $sizeUploadPath = $uploadPath . "{$size}/";
            if (!is_dir($sizeUploadPath)) mkdir($sizeUploadPath);
            $sizeName = $sizeUploadPath . $fileName;
            \Intervention\Image\Facades\Image::make($filePath)->fit($size, $size, function ($constraint) {
                $constraint->aspectRatio();
//                $constraint->upsize();
            })->save($sizeName);
        }

        $thumbUploadPath = $uploadPath . "thumb/";
        if (!is_dir($thumbUploadPath)) mkdir($thumbUploadPath);
        $thumbPath = $thumbUploadPath . $fileName;
        copy($uploadPath . "{$this->getThumbSize()}/" . $fileName, $thumbPath);

        return $this;
    }


    /**
     * set sizes
     *
     * @param array $sizes
     * @return $this
     */
    public function setSizes(array $sizes)
    {
        $this->sizes = $sizes;
        return $this;
    }


    /**
     * get current sizes
     *
     * @return array
     */
    public function getSizes()
    {
        return $this->sizes;
    }


    /**
     * set sizes
     *
     * @param $size
     * @return $this
     */
    public function setThumbSize($size)
    {
        $this->thumb = $size;
        return $this;
    }


    /**
     * get current sizes
     *
     * @return array
     */
    public function getThumbSize()
    {
        return $this->thumb;
    }
}
