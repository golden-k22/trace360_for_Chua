<style type="text/css">
        .voyager .compass .nav-tabs{
            background:none;
            border-bottom:0px;
        }

        .voyager .compass .nav-tabs > li{
            margin-bottom:-1px !important;
        }

        .voyager .compass .nav-tabs a{
            text-align: center;
            font-size: 12px;
            font-weight: normal;
            background: #f8f8f8;
            border: 2px solid #f1f1f1;
            position: relative;
            top: -1px;
            border-bottom-left-radius: 0px;
            border-bottom-right-radius: 0px;
        }

        .voyager .compass .nav-tabs a i{
            display: block;
            font-size: 22px;
        }

        .tab-content{
            background:#ffffff;
            border: 1px solid transparent;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .page-title{
            height:85px;
            z-index:2;
            position:relative;
            padding-left: 0px;
        }

        .page-content{
            /*z-index:2;*/
            position:relative;
        }

        .page-title p{
            height: 20px;
            margin-bottom: 0px;
            padding-top: 0px;
            position: relative;
            top: -10px;
        }

        /*.page-title span{*/
            /*font-size: 10px;*/
            /*font-weight: normal;*/
            /*top: -12px;*/
            /*position: relative;*/
        /*}*/

        #gradient_bg{
            position: fixed;
            top: 61px;
            left: 0px;
            {{--background-image: url({{ voyager_asset('images\widget-backgrounds\03.jpg') }});--}}
            background-image: url({{ voyager_asset('images\compass\documentation.jpg') }});
            background-size: cover;
            background-position: 0px;
            width: 100%;
            height: 220px;
            z-index: 0;
        }

        #gradient_bg::after{
            content:'';
            position:absolute;
            left:0px;
            top:0px;
            width:100%;
            height:100%;
            /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#f8f8f8+0,f8f8f8+100&0.95+0,1+80 */
            background: -moz-linear-gradient(top, rgba(248,248,248,0.93) 0%, rgba(248,248,248,1) 80%, rgba(248,248,248,1) 100%); /* FF3.6-15 */
            background: -webkit-linear-gradient(top, rgba(248,248,248,0.93) 0%,rgba(248,248,248,1) 80%,rgba(248,248,248,1) 100%); /* Chrome10-25,Safari5.1-6 */
            background: linear-gradient(to bottom, rgba(248,248,248,0.93) 0%,rgba(248,248,248,1) 80%,rgba(248,248,248,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f8f8f8', endColorstr='#f8f8f8',GradientType=0 ); /* IE6-9 */
            z-index:1;
        }

        .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover{
            background:#fff !important;
            color:#62a8ea !important;
            border-bottom:2px solid #fff !important;
            top:-1px !important;
        }

        .nav-tabs > li a{
            transition:all 0.3s ease;
        }


        .nav-tabs > li.active > a:focus{
            top:0px !important;
        }

        .voyager .compass .nav-tabs > li > a:hover{
            background-color: rgb(204, 204, 204) !important;
        }

        .voyager-link{
            width: 100%;
            min-height: 220px;
            display: block;
            border-radius: 3px;
            background-position: center center;
            background-size: cover;
            position:relative;
        }

        .voyager-link span.resource_label{
            text-align: center;
            color: #fff;
            display: block;
            position: absolute;
            z-index: 9;
            top: 0px;
            left: 0px;
            width: 100%;
            padding: 0px;
            opacity:0.8;
            transition:all 0.3s ease;
            line-height: 220px;
            height: 100%;
        }

        .voyager-link span.resource_label:hover{
            opacity:1;
        }

        .voyager-link i{
            font-size: 48px;
            margin-right: 0px;
            position: absolute;
            width: 70px;
            height: 70px;
            padding: 10px;
            border-radius: 5px;
            line-height: 55px;
            display: inline-block;
            left: 50%;
            margin-top: -50px;
            margin-left: -35px;
            top: 50%;
            line-height:55px;
            padding:10px;
        }

        .voyager-link span.resource_label:hover i{

            opacity:1;
            transition:all 0.3s linear;
        }

        .voyager-link span.copy{
            position: absolute;
            font-size: 16px;
            left: 0px;
            bottom: 70px;
            line-height: 12px;
            text-transform: uppercase;
            text-align: center;
            width: 100%;
        }

        h3 small{
            font-size: 11px;
            position: relative;
            top: -3px;
            left: 10px;
            color: #999;
        }

        .collapsible{
            margin-top:20px;
            border:1px solid #f7f7f7;
            border-radius:3px;
            display:block;
        }

        .collapse-head{
            border-bottom: 1px solid #f7f7f7;
            border-top-right-radius: 3px;
            padding:5px 15px;
            display:block;
            cursor:pointer;
            background:#ffffff;
            position:relative;
        }

        .collapse-head:after{
            content:'';
            clear:both;
            display:block;
        }

        .collapse-head h4{
            float:left;
        }

        .collapse-head i{
            font-size: 16px;
            position: absolute;
            top: 13px;
            right:15px;
            float:right;
        }

        .collapse-content{
            padding:15px;
            background:#fcfcfc;
        }

        .collapse-content .row{
            padding-bottom:0px;
            margin-bottom:0px;
        }

        .collapse-content .col-md-4{
            padding-right:0px;
            margin-bottom:0px;
        }

        .collapse-content .col-md-4:nth-child(3){
            padding-right:15px;
        }

        .voyager-link span.desc{
            font-size:11px;
            color:rgba(255, 255, 255, 0.8);
            width:100%;
            height:100%;
            position:absolute;
            text-align:center;
        }

        .voyager-angle-down{
            display:none;
        }

        .voyager-link::after{
            content:'';
            position:absolute;
            width:100%;
            height:100%;
background: -moz-linear-gradient(-65deg, rgba(17,17,17,0.7) 0%, rgba(35,35,47,0.7) 50%, rgba(17,17,23,0.7) 51%, rgba(17,17,23,0.7) 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(-65deg, rgba(17,17,17,0.7) 0%,rgba(35,35,47,0.7) 50%,rgba(17,17,23,0.7) 51%,rgba(17,17,23,0.7) 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(155deg, rgba(17,17,17,0.7) 0%,rgba(35,35,47,0.7) 50%,rgba(17,17,23,0.7) 51%,rgba(17,17,23,0.7) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b3111111', endColorstr='#b3111117',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
            left:0px;
            top:0px;
            border-radius:3px;
        }


    .tab-content > .active{
        display: block;
    }
    .toggle{
        width: 120px!important;
    }
    .action-work-detail{
        margin-left: 20px;
    }
    </style>