<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\WebsiteDetail;
use App\Testimonial;
use App\Traits\MediaTrait;
use Illuminate\Support\Facades\DB;
use Session, Image, Auth, Validator, File;

class WebsiteTestimonialController extends Controller
{
     use MediaTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data = [];

        if(isset($request->id)){
            $data["testimonials"] = Testimonial::where('website_id',$request->id)->get();
        }
            
        $data["websites"] = WebsiteDetail::where('company_id',session('company_id'))->where('status',1)->get();
        
        return view("websites.testimonial.index",$data);
    }

    public function create(Request $request)
    {
        return view("websites.testimonial.create", [
            "websites" => WebsiteDetail::where('company_id',session('company_id'))->where('status',1)->get()
        ]);
    }

    public function store(Request $request)
    {
        // |regex:/^[a-zA-Z]+$/u
        $this->validate($request, [
            "website_id"     => "required",
            "customer_name"  => "required|max:255|unique:website_testimonials",
            "rating"         => "required",
            "content"        => "required",
            "image"          => "required",
        ]);

        try {

            // if (Testimonial::where(['website_id' => $request->website_id, 'status' => 1, 'name' => $request->name])->count() > 0) {

            //     $this->validate($request, [
            //         "name"        => "required|max:255|unique:website_details",
            //     ]);
            // }

            $imageName = null;

            if (!empty($request->file('image'))){
                $image = $request->file('image');
                $request->validate([
                    'image' => 'mimes:jpeg,png,jpg,webp|max:1024',
                ]);

                $path = '/images/testimonials/';
                $returnImageValue = $this->uploads($image, $path);
                $imageName = $returnImageValue['fileName']; 
            }


            $website = Testimonial::create(array_merge(
                $request->except(["_token","image"]),
                ['logo' => $imageName]
            ));


            if (!isset($website->id)) {

                if ($imageLogo) {
                    $path = public_path('storage/images/testimonials/');
                    $this->removeImage($path,$imageName);
                }
                Session::flash('error', 'Server issue');
                return redirect()->route("website.create");
            }

            Session::flash('success', 'Success!');
            return redirect()->route("website.testimonial.index");
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->route("website.testimonial.create");
        }
    }

    public function edit(Request $request, $id)
    {

        $testimonial = Testimonial::where('id',$id)->first();
        if ($testimonial == null) {

            Session::flash('error', 'Record not found!');
            return redirect()->route('website.testimonial.index');
        }


        return view("websites.testimonial.edit", [
            "testimonial" => $testimonial,
            "websites"    => WebsiteDetail::where('company_id',session('company_id'))->where('status',1)->get()
        ]);
    }

    public function update(Request $request, $id)
    {

        $website_detail = WebsiteDetail::find($id);

        $this->validate($request, [
            // "company_id"  => "required",
            "type"        => "required",
            // "theme"       => "required",
            "url"         => "required",
        ]);

        try {


            $websiteName  = strtolower(str_replace(array(" ", "'"), '-', $request->post('name')));

            if (!empty($request->favicon)) {
                $request->validate([
                    'favicon' => 'mimes:jpeg,png,jpg,gif,svg,webp|min:10|max:100',
                ]);


                if (\File::exists(public_path('storage/images/website/' . $website_detail->favicon))) {
                    \File::delete(public_path('storage/images/website/' . $website_detail->favicon));
                }

                $imageFavicon = $websiteName . '-favicon.' . $request->file('favicon')->getClientOriginalExtension();
                $img = Image::make($request->file('favicon'))->resize(64, 64);
                $res0 = $img->save(public_path('storage/images/website/' . $imageFavicon), 90);
            }

            if (!empty($request->logo)) {

                $request->validate([
                    'logo' => 'mimes:jpeg,png,jpg,gif,svg,webp|min:10|max:100',
                ]);


                if (\File::exists(public_path('storage/images/website/' . $website_detail->logo))) {
                    \File::delete(public_path('storage/images/website/' . $website_detail->logo));
                }

                $imageLogo = $websiteName . '-logo.' . $request->file('logo')->getClientOriginalExtension();
                // $img = Image::make($request->file('logo'))->resize(200, 200);
                // $res1 = $img->save(public_path('storage/images/website/' . $imageLogo), 75);

                $getLogo = $request->file('logo');
                $getLogo->move(public_path('storage/images/website/'), $imageLogo);
            }


            if ($website_detail->name  != $request->name) {
                // regex:/^[a-zA-Z]+$/u
                $rule = [
                    "name" => "required|max:255|unique:website_details",
                ];
                $this->validate($request, $rule);
            }

            $website_detail->type        = $request->type;
            $website_detail->name        = $request->name;
            $website_detail->url         = $request->url;
            $website_detail->whatsapp    = $request->whatsapp;
            $website_detail->uan_number  = $request->uan_number;

            if (isset($imageLogo)) {
                $website_detail->logo   = $imageLogo;
            }



            $website_detail->save();


            return redirect()->route("website.testimonial.index");
        } catch (Exception $e) {
            return redirect()->route("website.testimonial.edit", $website_detail->id);
        }
    }

    public function destroy(Request $request, $id)
    {
        $getRecord = Testimonial::where('id',$id)->where('website_id',$request->webid)->first();

        if ($getRecord == null) {
            Session::flash('error', 'Error! record not found! Server Issue!');
            return redirect()->route("website.testimonial.index");
        }

        if (Testimonial::where('id',$id)->where('website_id',$request->webid)->delete()) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Error! this ' . $getRecord->name . ' testimonial is not removed for this '.$request->website.' !');
        }
        return redirect()->route("website.testimonial.index");
    }



}
