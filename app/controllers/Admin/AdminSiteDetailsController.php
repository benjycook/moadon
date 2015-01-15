<?php

class AdminSiteDetailsController extends BaseController 
{
	protected function rules()
	{
		$rules = array(
			'supplierName'=> "required",'description'=> "required",'miniSiteContext'=> "required",'workingHours'=> "required",
			'ageDevision'=> "required",'phone2'=> "required"
			);

		return $rules;
	}
	protected function validateCaregoriesAndRegions($data)
	{
		if(!count($data['categories']))
			return array('error'=>'יש לבחור לפחות קטגוריה אחת');
		if(!count($data['regions']))
			return array('error'=>'יש לבחור לפחות אזור אחת');
		if(Category::whereIn('id',$data['categories'])->count()!=count($data['categories']))
			return array('error'=>'אחת הקטגוריות לא נמצא במערכת');
		if(Region::whereIn('id',$data['regions'])->count()!=count($data['regions']))
			return array('error'=>'אחת האזורים לא נמצא במערכת');
		return false;
	}
	protected function galleriesImages($images,$galleryId)
	{
		$ids = array(0);
		$tempDirPath = public_path()."/galleries/tempimages/";
		$galleryDir =  public_path()."/galleries/";
		foreach ($images as $img) {
			$img['galleries_id'] = $galleryId;
			$ext  = explode($img['id'],$img['fullSrc']);
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
			}
		}
		$oldImages = GalleryImage::where('galleries_id','=',$galleryId)->whereNotIn('id',$ids)->get();
		foreach ($oldImages as $img) 
		{
			File::delete($galleryDir."".$img['src']);
			$img->delete();
		}
	}
	// public function store()
	// {
	// 	$json   = Request::getContent();
 //    	$data   = json_decode($json,true);
 //    	$siteDetails 	= new SiteDetails;
 //    	$validator = Validator::make($data,$this->rules());
 //    	if($validator->fails())
 //    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
 //    	if(!Supplier::where('id','=',$data['suppliers_id'])->count())
 //    		return Response::json(array('error'=>"ספק זה לא נמצא במערכת"),501);
 //    	$siteDetails = $siteDetails->create($data);
 //    	$images = $data['galleries']['main']['images'];
	// 	$counter = 1;
	// 	foreach($images as &$img) {
	// 		if(!strpos($img['fullSrc'],"?"))
	// 			unset($images[array_search($img,$images)]);
	// 		else
	// 		{
	// 			$img['pos'] = $counter;
	// 			$counter++;
	// 		}
	// 	}
	// 	$gallery = Gallery::create(array('type'=>'ראשית'));
 //    	$gallery->images = $images;
	// 	$this->galleriesImages($gallery->images,$gallery->id);
	// 	$siteDetails->galleries()->attach($gallery->id);
	// 	$siteDetails = SiteDetails::with('galleries')->find($siteDetails['id'])->toArray();
 //    	$siteDetails['linkId'] = $siteDetails['id'];
	// 	$temp = array();
	// 	$temp['main'] = isset($siteDetails['galleries'][0]) ? $siteDetails['galleries'][0]:array('images'=>array());
	// 	$temp['main']['base'] = URL::to('/')."/galleries/";
	// 	$siteDetails['galleries'] = $temp;
	// 	$siteDetails['uploadUrl'] = '/uploadImage';
 //    	return Response::json($siteDetails,201);
	// }

	public function update($id)
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
		$siteDetails = SiteDetails::find($id);
		if(!$siteDetails)
			return Response::json(array('error'=>'פרטי אתר זה לא נמצא במערכת'),501);
    	$validator = Validator::make($data,$this->rules());
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	if(!Supplier::where('id','=',$data['suppliers_id'])->count())
    		return Response::json(array('error'=>"ספק זה לא נמצא במערכת"),501);

    	foreach ($data['galleries'] as $gallery) 
		{
			$counter = 1;
			if(!isset($gallery['id']))
			{
				$new = Gallery::create(array('type'=>'ראשית'));
				$gallery['id'] = $new->id;
				$siteDetails->galleries()->attach($new->id);
			}
			foreach ($gallery['images'] as &$img) {
				if(!strpos($img['fullSrc'],"?"))
					unset($gallery['images'][array_search($img,$gallery['images'])]);
				else
				{
					$img['pos'] = $counter;
					$counter++;
				}
			}
			$this->galleriesImages($gallery['images'],$gallery['id']);
		}
    	$siteDetails->fill($data);
    	$siteDetails->save();
    	$siteDetails = SiteDetails::with('galleries')->find($id)->toArray();
    	$siteDetails['linkId'] = $siteDetails['id'];
    	$supplier = Supplier::find($siteDetails['suppliers_id']);
    	$res = $this->validateCaregoriesAndRegions($data);
    	if(isset($res['error']))
    		return Response::json(array('error'=>$res['error']),501);
    	// $supplier->categories()->attach($data['categories']);
    	// $supplier->regions()->attach($data['regions']);
    	$supplier->categories()->sync($data['categories']);
    	$supplier->regions()->sync($data['regions']);
		$temp = array();
		$temp['main'] = isset($siteDetails['galleries'][0]) ? $siteDetails['galleries'][0]:array('images'=>array());
		$temp['main']['base'] = URL::to('/')."/galleries/";
		$siteDetails['galleries'] = $temp;
		$siteDetails['uploadUrl'] = '/uploadImage';
    	return Response::json($siteDetails,201);
	}

}