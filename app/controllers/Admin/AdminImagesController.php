<?php 
class AdminImagesController extends BaseController 
{

	public function uploadImage($id)
	{
		if (Input::hasFile('file'))
		{
			$file = Input::file('file');
			$data = array('image'=>$file);
			$rules = array( 
    			'image' => 'mimes:jpg,png,jpeg,gif'
				);
			$validator = Validator::make($data, $rules);
			if ($validator->fails()) 
			{
				$messages=$validator->messages()->toArray();
				$message =$messages['image'];
				return Response::json(array('error'=>'באפשרותך לעלות רק קבצים מסוג: jpg,gif,png.'), 400);
			}
			$image_info = getimagesize($file);
			// if(filesize($file)>1048576)//
			// 	return Response::json(array('error'=>'באפשרותך לעלות רק קבצים מסוג: jpg, png. גודל מקסימלי 1MB'), 400);
			$path = public_path()."/galleries/tempimages";
			$fileName = "$id.jpg";
    		$file = Input::file('file')->move($path,$fileName);
    		$fileUrl = URL::to('/')."/galleries/tempimages/$fileName";
			return Response::json(array('link'=>$fileUrl,'fileUpload'=>true), 200);
		}
	}

	public function galleriesImages($images,$galleryId)
	{
		$ids = array(0);
		$tempDirPath = public_path()."/galleries/tempimages/";
		$galleryDir =  public_path()."/galleries/";
		foreach ($images as $img) {
			$img['galleries_id'] = $galleryId;
			$ext  = explode($img['id'],$img['fullSrc']);
			if(count($ext)!=2)
				continue;
			$ext  = substr($ext[1],0,strpos($ext[1],"?"));
			$img['src'] = $img['id']."".$ext;
			if(!$galleryimage = GalleryImage::find($img['id']))
			{
				$fileName = $img['id']."".$ext;
				$img['src'] = "";
				$galleryimage = new GalleryImage;
				$galleryimage = $galleryimage->create($img);
				$img['src']   = $galleryimage->id."$ext";
				File::move($tempDirPath."".$fileName,$tempDirPath."".$galleryimage->id."".$ext);
			}
			$ids[] = $galleryimage->id;
			$galleryimage->fill($img);
			$galleryimage->save();
			if(File::exists($tempDirPath."".$img['src']))
			{
				$files = glob($galleryDir."$galleryimage->id.*");
				foreach ($files as $file) {
				  File::delete($file);
				}
				File::move($tempDirPath."".$galleryimage->src,$galleryDir."".$galleryimage->src);
				//File::copy($galleryDir."".$galleryimage->src,public_path()."/backup/".$galleryimage->src);
			}
		}
		$oldImages = GalleryImage::where('galleries_id','=',$galleryId)->whereNotIn('id',$ids)->get();
		foreach ($oldImages as $img) 
		{
			File::delete($galleryDir."".$img['src']);
			$img->delete();
		}
	}
}	
