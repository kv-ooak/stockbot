<?php

namespace App\Managers;

use App\DataFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class FileManager {

    public static $DATA_TYPE = array(
        DataType::Ticker,
        DataType::RawData,
        DataType::Quote,
        DataType::Unknown,
    );

    /**
     * 
     * @return type
     */
    public static function getFileList() {
        return DataFile::all();
    }

    /**
     * 
     * @param string $filename
     * @return type
     */
    public static function getFileByName($filename) {
        return DataFile::where('filename', $filename)->first();
    }

    /**
     * 
     * @param string $original_filename
     * @return type
     */
    public static function getFileOriginalName($original_filename) {
        return DataFile::where('original_filename', $original_filename)->first();
    }

    /**
     * 
     * @param type $original_filename
     * @return type
     */
    public static function getFilePathByOriginalName($original_filename) {
        $file = DataFile::where('original_filename', $original_filename)->first();
        if (!isset($file) || !Storage::disk('local')->has($file->filename)) {
            return null;
        }
        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        return $storagePath . $file->filename;
    }

    /**
     * 
     * @param integer $data_type
     * @return type
     */
    public static function getFileByType(integer $data_type) {
        return DataFile::where('data_type', $data_type);
    }

    /**
     * 
     * @param type $file
     * @param type $data_type
     * @return type
     */
    public static function addFile($file, $data_type) {
        /*
          $rules = array('file' => 'required|mimes:txt,csv');
          $validator = Validator::make($file, $rules);

          if ($validator->fails()) {
          return Response::json([
          'error' => true,
          'message' => $validator->messages()->first(),
          'code' => 400
          ], 400);
          }
         * 
         */

        try {
            $extension = $file->getClientOriginalExtension();
            Storage::disk('local')->put($file->getFilename() . '.' . $extension, File::get($file));
            $entry = new DataFile();
            $entry->mime = $file->getClientMimeType();
            $entry->original_filename = $file->getClientOriginalName();
            $entry->filename = $file->getFilename() . '.' . $extension;
            $entry->data_type = in_array($data_type, FileManager::$DATA_TYPE) ? $data_type : DataType::Unknown;
            $entry->save();
            return Response::json([
                        'error' => false,
                        'code' => 200
                            ], 200);
        } catch (\Exception $e) {
            return Response::json([
                        'error' => true,
                        'message' => 'Server error while uploading. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * 
     * @param type $filename
     * @return type
     */
    public static function deleteFile($filename) {
        Storage::disk('local')->delete($filename);
        DataFile::where('filename', $filename)->delete();
    }

}
