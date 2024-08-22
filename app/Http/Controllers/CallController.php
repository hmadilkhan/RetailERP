<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Artisaninweb\SoapWrapper\SoapWrapper;
 
class CallController extends Controller
{
      protected $soapWrapper;
 
   
   public function __construct(SoapWrapper $soapWrapper)
   {
     $this->soapWrapper = $soapWrapper;
   }
 
   public function index()
   {
      return $this->soapWrapper->add('Holidays', function ($service) {
       $service
         ->wsdl('service.wsdl')
         ->trace(true);
     });
 



   }
 
}