<?php
/**
 * Created by SERJIK
 */

Route::get("/download/{file}", "DownloadController@download");
Route::get("/download/image/{size}/{file}", "ImageDownloadController@image");
