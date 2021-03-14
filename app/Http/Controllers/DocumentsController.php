<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\DocumentResource;
use Illuminate\Support\Facades\Schema;
use App\Models\Document;
use Storage;

class DocumentsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'document' => 'required|mimes:pdf,png,jpg|max:9999',
        ]);

        $base_location = 'user_documents';

        // Handle File Upload
        if($request->hasFile('document')) {              
            //Using store(), the filename will be hashed. You can use storeAs() to specify a name.
            //To specify the file visibility setting, you can update the config/filesystems.php s3 disk visibility key,
            //or you can specify the visibility of the file in the second parameter of the store() method like:
            //$documentPath = $request->file('document')->store($base_location, ['disk' => 's3', 'visibility' => 'public']);
            
            $documentPath = $request->file('document')->store($base_location, 's3');
          
        } else {
            return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }
    
        //We save new path
        $document = new Document();
        $document->path = $documentPath;
        $document->name = $request->name;
        $document->save();
       
        return response()->json(['success' => true, 'message' => 'Document successfully uploaded', 'document' => new DocumentResource($document)], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::find($id);

        if(empty($document)){
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        //We remove existing document
        if(!empty($document))
        {
            Storage::disk('s3')->delete($document->path);
            $document->delete();
            return response()->json(['success' => true, 'message' => 'Document deleted'], 200);
        }

        return response()->json(['success' => false, 'message' => 'Unable to delete the document. Please try again later.'], 400);
    }

    /**
     * get.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDocuments()
    {
        $documents = Document::all();
        return response([ 'documents' => DocumentResource::collection($documents), 'message' => 'Retrieved successfully'], 200);
    }
    
    /**
     * update.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateDocument($id, Request $request)
    {
        $document = Document::find($id);

        if(empty($document)){
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        $this->validate($request, [
            'name' => 'required',
            'document' => 'required|mimes:pdf,png,jpg|max:9999',
        ]);

        $base_location = 'user_documents';

        // Handle File Upload
        if($request->hasFile('document')) {              
            $documentPath = $request->file('document')->store($base_location, 's3');
          
        } else {
            return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }
        $document->path = $documentPath;
        $document->name = $request->name;
        $document->save();
       
        return response()->json(['success' => true, 'message' => 'Document successfully uploaded', 'document' => new DocumentResource($document)], 200);
    }
    
}