<?php
namespace App;

class MyConst{
    //request table
    public const NURSER_REQUEST = 1;
    public const PATIENT_REQUEST =2;
    //status request table
    public const REQUESTING = 2;
    public const ACCEPTED =1;
    public const CANCEL =0;
    //type user
    public const NURSE =1;
    public const PATIENT =2;
    //notification type
    public const NOTI_PATIENT_REQUEST =1;
    public const NOTI_PATIENT_CANCEL =2;
    public const NOTI_PATIENT_ACCEPT = 3;
    public const NOTI_NURSE_REQUEST = 4;
    public const NOTI_NURSE_CANCEL =5;
    public const NOTI_NURSE_ACCEPT =6;
}