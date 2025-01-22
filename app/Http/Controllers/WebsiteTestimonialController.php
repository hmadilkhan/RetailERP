<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\WebsiteDetail;
use App\Testimonial;
use App\Traits\MediaTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
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
            $data['websiteId']=$request->id;
            $data["testimonials"] = Testimonial::where('website_id',$request->id)
                                                ->orderBy('website_id','DESC')
                                                ->get();
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
            "image"          => 'nullable|mimes:jpg,jpeg,png,webp|max:1024',
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


            $testimonial = Testimonial::create(array_merge(
                $request->except(["_token","image"]),
                ['image' => $imageName]
            ));


            if (!isset($testimonial->id)) {

                if ($imageName) {
                    $path = 'images/testimonials/';
                    $this->removeImage($path,$imageName);
                }
                Session::flash('error', 'Server issue');
                return redirect()->route("testimonials.create");
            }

            Session::flash('success', 'Success!');
            return redirect()->route("filterTestimonial",$request->website_id);
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->route("testimonials.create");
        }
    }

    public function edit(Request $request, $id)
    {

        $testimonial = Testimonial::where('id',$id)->first();
        if ($testimonial == null) {

            Session::flash('error', 'Record not found!');
            return redirect()->route('testimonials.index');
        }


        return view("websites.testimonial.edit", [
            "testimonial" => $testimonial,
            "websites"    => WebsiteDetail::where('company_id',session('company_id'))->where('status',1)->get()
        ]);
    }

    public function update(Request $request, $id)
    {

        $testimonial = Testimonial::find($id);

        $this->validate($request, [
            "website_id"     => "required",
            "customer_name"  => "required|max:255",
            "rating"         => "required",
            "content"        => "required",
        ]);

        try {


            if (!empty($request->file('image'))){
                $image = $request->file('image');
                $request->validate([
                    'image' => 'mimes:jpeg,png,jpg,webp|max:1024',
                ]);

                $path = '/images/testimonials/';
                $returnImageValue = $this->uploads($image, $path,$testimonial->image);
                $imageName = !empty($returnImageValue['fileName']) ? $returnImageValue['fileName'] : null;
            }



            if ($testimonial->customer_name  != $request->customer_name) {
                // regex:/^[a-zA-Z]+$/u
                $rule = [
                    "customer_name" => "required|max:255|unique:website_testimonials",
                ];
                $this->validate($request, $rule);
            }

            $testimonial->website_id      = $request->website_id;
            $testimonial->customer_name   = $request->customer_name;
            $testimonial->rating          = $request->rating;
            $testimonial->content         = $request->content;

            if (isset($imageName)) {
                $testimonial->image   = $imageName;
            }



            $testimonial->save();


            return redirect()->route("filterTestimonial",$request->website_id);
        } catch (Exception $e) {
            return redirect()->route("testimonials.edit", $testimonial->id);
        }
    }

    public function destroy(Request $request, $id)
    {
        $websiteId = Crypt::decrypt($request->websiteId);
        $getRecord = Testimonial::where('id',$id)->where('website_id',$websiteId)->first();

        if ($getRecord == null) {
            Session::flash('error', 'Error! record not found! Server Issue!');
            return redirect()->route("filterTestimonial",$websiteId);
        }

        if (Testimonial::where('id',$id)->where('website_id',$websiteId)->delete()) {
            $this->removeImage('/images/testimonials/',$getRecord->image);
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Error! this ' . $getRecord->customer_name . ' testimonial is not removed for this '.$request->website.' !');
        }
        return redirect()->route("filterTestimonial",$websiteId);
    }



}
