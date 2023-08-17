<?php
$report_id = $_POST['pdf_report_id'];
$outfiles = get_data_outfile();
$address      = $report_id ? get_field('address' , $report_id) : '';
$report_files = $report_id ? get_field('report_files' , $report_id) : '';
$property_zone = $report_id ? get_field('property_zone' , $report_id) : '';
$property_size = $report_id ? get_field('property_size' , $report_id) : '';
$lotlocationdp = $report_id ? get_field('lotlocationdp' , $report_id) : '';
$council = $report_id ? get_field('council' , $report_id) : '';
$author_id = get_post_field( 'post_author', $report_id );
$google_map_img = get_field('google_map_image',$report_id);
$discount = get_field('discount', 'user_' . $author_id);
$total = 0;
$company = get_user_meta($author_id,'user_registration_input_box_company',true);
$phone   = get_user_meta($author_id,'user_registration_input_box_phone',true);
$term_obj_list = get_the_terms( $report_id, 'type-reports' );
$types = trim(join("", wp_list_pluck($term_obj_list, "name")));
$sub_head_box1 = "";
if($types == 'Deck') $sub_head_box1    = "PlanDat Deck Report";
if($types == 'Carport') $sub_head_box1 = "PlanDat Carport Report";
if($types == 'House') $sub_head_box1   = "PlanDat House Report";
if($types == 'Pergola') $sub_head_box1 = "PlanDat Pergola Report";
if($types == 'Pool') $sub_head_box1    = "PlanDat Pool Report";
if($types == 'Shed') $sub_head_box1    = "PlanDat Class 10a Shed Report";
$post_date = get_the_date( 'm/d/y' , $report_id);
$zones = explode(',',$property_zone);
$check_zones = [];
foreach ($zones as $zone){
  $zone = trim($zone);
  $check_zones[] = strtolower($zone);
  $check_zones[] = strtolower(str_replace(':','',$zone));
  $check_zones[] = strtolower('Zone ' . str_replace(':','',$zone));
}
//1. Trim Council Name
$trim_council_name = strtolower(trim(str_replace('COUNCIL','',$council)));

//2. Find Council In Outfile
$list_zones = [];
foreach ($outfiles as $key => $row) {
  $Council = strtolower($row['Council']);
  $Z	= strtolower(trim($row['Zone']));
  if(strpos($Council, $trim_council_name) !== false && in_array($Z,$check_zones)){
    $list_zones[] = $row;
  }
}
?>
<html>
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
      <title>PlanDAT Shed NSW Report</title>
      <style>
          /**
              Set the margins of the page to 0, so the footer and the header
              can be of the full height and width !
           **/
          @page {
              margin: 0cm 0cm;
          }
          html {
              -webkit-print-color-adjust: exact;
              -webkit-filter: opacity(1);
          }

          /** Define now the real margins of every page in the PDF **/
          body {
              margin-top: 120px;
              margin-left: 2cm;
              margin-right: 2cm;
              margin-bottom: 90px;
              font-family: sans-serif;
          }

          /** Define the header rules **/
          header {
              position: fixed;
              top: 15px;
              left: 0cm;
              right: 0cm;
              height: 103px;
          }

          /** Define the footer rules **/
          footer {
              position: fixed;
              bottom: 15px;
              left: 0cm;
              right: 0cm;
              height: 73px;
          }

          main{
            padding: 40px 0;
          }

          table{
  					  width: 100%;
  				}

  				table th,table td{
  					padding: 10px;
  					color: #fff;
            width: 100%;
            height: auto;
  				}

  				table th,table tr td:first-child{
  					background: #ff6d20;
  				}

  				table tr td:not(:first-child){
  					background: #fbc9ae;
  				}

  				table tr:nth-child(2n) td:not(:first-child){
  					background: #ffe9de;
  				}

          .page_break { page-break-after: always; }

          .clear{
            clear: both;
          }

          /* Box */
          .box-pdf{
            padding: 30px;
            box-shadow: 0px 0px 8px 0px #969696;
            border-radius: 4px;
            border: 1px solid #969696;
            margin-bottom: 10px;
          }
          .box-pdf .box-head h2{
            float: left;
            width: 70%;
            margin: 0;
            padding: 0;
            color: #ff6d20;
            text-transform: uppercase;
          }

          .box-pdf .box-head time{
            float: right;
            width: 30%;
            text-align: right;
            color: gray;
          }

          .box-pdf .box-head time b{
            color: #222;
          }

          .box-pdf>h3{
            margin-top: 0;
          }

          .box-content h3{
            margin: 0;
            font-size: 20px;
            color: #000000;
            font-weight: 400;
          }

          .box-2{
            height: 250px;
          }

          .box-2 .box-head h2{
            float: left;
            width: 18%;
            font-size: 18px;
            text-transform: capitalize;
            margin: 10px 0;
          }

          .box-2 .box-head img{
            width: 80%;
            float: right;
            padding: 0 50px;
            max-width: 75%;
          }

          .box-content .ul,.box-content ul{
            padding: 0;
            margin-top: 15px;
          }

          .box-content .ul .li,.box-content ul li{
            list-style: none;
            clear: both;
          }

          .box-content .ul .li span{
            width: 64%;
            float: left;
            display: block;
            border: 1px solid #bfbfbf;
            padding: 5px 0;
            font-size: 14px;
            margin-bottom: 8px;
          }

          .box-content .ul .li:not(:last-child) span{
            margin-bottom: 0px;
          }

          .box-content .ul .li span.label{
            border-right: none;
            padding-left: 10px;
          }

          .box-content .ul .li span.value{
            border-left: none;
          }

          .box-content .ul .li span.label{
            font-weight: bold;
            width: 35%;
          }

          ._img img{
            width: 100%;
            margin-top: 20px;
          }

          .box-4{
            padding-bottom: 10px;
            padding-top: 20px;
          }

          .box-4 ul{
            margin-top: 10px;
          }

          .box-4 ul li{
            font-size: 14px;
            margin-bottom: 5px;
          }

          .box-table-image .box-content ._table{
            width: 32%;
            float: left;
          }

          .box-table-image{
            padding: 0;
            border: none;
          }

          .box-table-image .box-content ._img{
            width: 63% !important;
            float: left;
            padding: 10px;
            border: 1px solid #afafaf;
            border-radius: 4px;
            margin-left: 10px;
          }

          .box-table-image .box-content ._table table{
            width: 100%;
          }

          .box-table-image .box-content ._table table tr td{
            padding: 5px 10px;
            font-weight: 500;
            font-size: 14px;
          }

          .box-table-image .box-content ._table table tr td:first-child{
            width: 70%;
            background: #ff6d20;
            color: #fff;
          }

          .box-table-image .box-content ._table table tr td:last-child{
            width: 30%;
            background: #ffe9de;
          }

          .box-table-image .box-content ._table table tr:nth-child(2n) td:last-child{
            background: #fbc9ae;
          }

          .box-6,.box-8,.box-10,.box-12{
            padding: 20px 30px;
          }

          .box-6 .box-head h2,.box-8 .box-head h2,.box-10 .box-head h2{
            font-size: 20px;
            width: 100%;
            margin-bottom: 0;
          }

          .box-12 .box-content h3{
  					font-weight: bold;
  				}

          .box-12 .box-content p{
            font-size: 14px;
          }

          .box-12 .box-head h2{
            width: 100%;
            float: none;
            font-size: 20px;
          }

      </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
       <header>
           <img src="<?php echo PLANDAT_PDF_URL . 'public/images/PlanDAT-header.png' ?>" width="100%" height="100%"/>
       </header>

       <footer>
           <img src="<?php echo PLANDAT_PDF_URL . 'public/images/PlanDAT-footer.png' ?>" width="100%" height="100%"/>
       </footer>

       <!-- Wrap the content of your PDF inside a main tag -->
       <main>
           <div class="box-pdf box-1">
              <div class="box-head">
                  <h2><?php echo $types; ?></h2>
                  <time>
                    <b>Date:</b> <?php echo $post_date; ?>
                  </time>
              </div>
              <div class="clear"></div>
              <div class="box-content">
                  <h3><?php echo $sub_head_box1; ?></h3>
                  <div class="ul">
                    <div class="li"> <span class="label">Address</span> <span class="value"><?php echo $address; ?></span> </div>
                    <div class="clear"></div>
                    <div class="li"> <span class="label">Zone</span> <span class="value"><?php echo $property_zone; ?></span> </div>
                    <div class="clear"></div>
                    <div class="li"> <span class="label">Council</span> <span class="value"><?php echo $council; ?></span> </div>
                    <div class="clear"></div>
                    <div class="li"> <span class="label">Lot/Section/Plan No.</span> <span class="value"><?php echo $lotlocationdp; ?></span> </div>
                    <div class="clear"></div>
                    <div class="li"> <span class="label">Property Size</span> <span class="value"><?php echo $property_size; ?></span> </div>
                    <div class="clear"></div>
                  </div>
              </div>
           </div>

           <div class="box-pdf box-2">
             <div class="box-head">
                 <h2>Summary of Affects</h2>
                 <img src="<?php echo PLANDAT_PDF_URL . 'public/images/Summary of Effects.png' ?>" alt="">
             </div>
           </div>

           <div class="box-pdf box-3">
             <div class="box-head">
                 <h2>PROPOSED STRUCTURE</h2>
             </div>
             <div class="clear"></div>
             <div class="box-content">
               <h3>Class 10a Shed</h3>
               <h3>Ancillary Structure</h3>
               <h3>Rural/Residential</h3>
             </div>
           </div>

           <div class="box-pdf box-4">
             <div class="box-head">
                 <h2>APPROVAL PATHWAYS</h2>
             </div>
             <div class="clear"></div>
             <div class="box-content">
               So, you want your <b><?php echo $types; ?></b> approved, there are multiple approval pathways to consider;
               <ul>
                 <li> <b>1. Exempt Development:</b> An ‘Approval’ pathway which avoids an approving authority, to have an exempt development you will need to meet all exempt planning guidelines as outlined under the State Environmental Planning Policy (Exempt and Complying Development Codes) 2008 (‘SEPP’). Note: ED must be declared to council. </li>
                 <li> <b>2. Complying Development Certificate (‘CDC Approval’):</b> Complying Development Approval is provided by a private certifier or council under the SEPP. These guidelines are more flexible than the exempt guidelines under the SEPP, however all must be met as well. The CDC pathway is generally completed under a private certifier and is considered the faster approval pathway compared to council. </li>
                 <li> <b>3. Council Approval - Development Application (‘DA’) and Construction Certificate (‘CC’):</b> This pathway allows for the greatest variation of guidelines to meet your site. The guidelines for Council Approval or DA/CC approval are found within the council Development Control Plan. Depending on the council you are located in, you may have flexibility for merit-based assessment and variation to these guidelines. </li>
               </ul>
             </div>
           </div>

           <div class="box-pdf box-5">
             <div class="box-head">
                 <h2>APPROVAL PATHWAY OPTIONS</h2>
             </div>
             <div class="clear"></div>
             <div class="_img">
               <img src="<?php echo PLANDAT_PDF_URL . 'public/images/Approval Pathway Options.png' ?>" alt="">
             </div>
           </div>

           <div class="box-pdf box-6">
             <div class="box-head">
                 <h2>YOUR GUIDELINES FOR YOUR PROPERTY!</h2>
             </div>
             <div class="clear"></div>
             <h3>Exempt Pathway</h3>
             <div class="_img">
               <img src="<?php echo PLANDAT_PDF_URL . 'public/images/Exempt Development.png' ?>" alt="">
             </div>
           </div>
           <div class="clear"></div>
           <div class="box-pdf box-7 box-table-image">
             <div class="box-content">
               <div class="_table">
                 <table>
                     <tbody>
                       <tr>
                         <td>Council Name</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Size</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Floor Area</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Height</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Front Setback</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Rear Setback</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Side Setback</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Cut</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Fill</td>
                         <td></td>
                       </tr>
                     </tbody>
                 </table>
               </div>
               <div class="_img">
                  <img src="<?php echo PLANDAT_PDF_URL . 'public/images/Plans_RESIDENTIAL EXEMPT.png' ?>" alt="">
               </div>
             </div>
           </div>
           <div class="clear"></div>
           <div class="box-pdf box-8">
             <div class="box-head">
                 <h2>YOUR GUIDELINES FOR YOUR PROPERTY!</h2>
             </div>
             <div class="clear"></div>
             <h3>Complying Development Pathway</h3>
             <div class="_img">
               <img src="<?php echo PLANDAT_PDF_URL . 'public/images/Complying Development.png' ?>" alt="">
             </div>
           </div>
           <div class="clear"></div>
           <div class="box-pdf box-9 box-table-image">
             <div class="box-content">
               <div class="_table">
                 <table>
                     <tbody>
                       <tr>
                         <td>Council Name</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Size</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Floor Area</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Height</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Front Setback</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Rear Setback</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Side Setback</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Cut</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Fill</td>
                         <td></td>
                       </tr>
                     </tbody>
                 </table>
               </div>
               <div class="_img">
                  <img src="<?php echo PLANDAT_PDF_URL . 'public/images/Plans_RESIDENTIAL CDC.png' ?>" alt="">
               </div>
             </div>
           </div>
           <div class="clear"></div>
           <div class="box-pdf box-10">
             <div class="box-head">
                 <h2>YOUR GUIDELINES FOR YOUR PROPERTY!</h2>
             </div>
             <div class="clear"></div>
             <h3>Development Approval Pathway</h3>
             <div class="_img">
               <img src="<?php echo PLANDAT_PDF_URL . 'public/images/Development Application.png' ?>" alt="">
             </div>
           </div>
           <div class="clear"></div>
           <div class="box-pdf box-11 box-table-image">
             <div class="box-content">
               <div class="_table">
                 <table>
                     <tbody>
                       <tr>
                         <td>Council Name</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Size</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Floor Area</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Height</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Front Setback</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Rear Setback</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Side Setback</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Cut</td>
                         <td></td>
                       </tr>
                       <tr>
                         <td>Fill</td>
                         <td></td>
                       </tr>
                     </tbody>
                 </table>
               </div>
               <div class="_img">
                  <img src="<?php echo PLANDAT_PDF_URL . 'public/images/Plans_RURAL DA.png' ?>" alt="">
               </div>
             </div>
           </div>
           <?php
            foreach ($zones as $key => $zone) {
               $zone_new = trim($zone);
               $zone_cus = [];
               $zone_cus[] = strtolower($zone_new);
               $zone_cus[] = strtolower(str_replace(':','',$zone_new));
               $zone_cus[] = strtolower('Zone ' . str_replace(':','',$zone_new));
               $data_zone = [];
               foreach ($list_zones as $kz => $d) {
                 $l_zone = trim(strtolower($d['Zone']));
                 if(in_array($l_zone, $zone_cus)){
                   $data_zone = $d;
                 }
               }
               ?>
               <div class="page_break">
                   <div class="clear"></div>
                   <div class="box-pdf box-zone">
                      <div class="box-content">
                          <div class="box-head">
                              <h2>LAND USE</h2>
                          </div>
                          <div class="ul">
                            <div class="li"> <span class="label">Zone</span> <span class="value"><?php echo $zone ?></span> </div>
                            <div class="clear"></div>
                          </div>
                      </div>
                   </div>
                   <div class="clear"></div>
                   <div class="box-pdf box-zone">
                      <div class="box-content">
                          <div class="box-head">
                              <h2>USE AND PERMISSIBILITY</h2>
                          </div>
                          <div class="clear"></div>
                          <h3>Can Do</h3>
                          <b>Permitted without consent:</b>
                          <p><?php echo $data_zone['Use Permitted Without Consent'] ?></p>
                          <b>Permitted with consent:</b>
                          <p><?php echo $data_zone['Use Permitted with Consent'] ?></p>
                          <br>
                          <h3>Can’t Do</h3>
                          <b>Prohibited</b>
                          <p><?php echo $data_zone['Prohibited Use'] ?></p>
                      </div>
                   </div>
                 </div>
               <?php
           } ?>

        	 <div class="clear"></div>
        	 <div class="box-pdf box-12">
        			 <div class="box-head">
        					 <h2>SITE HAZARDS AND ENVIRONMENTAL CONSIDERATIONS</h2>
        			 </div>
        			 <div class="clear"></div>
        			 <img src="<?php echo PLANDAT_PDF_URL . 'public/images/Bushfire.png' ?>" width="130" alt="">
        			 <br>
        			<div class="box-content">
        					<h3>Bushfire Prone Land – Category XXX</h3>
        					<p> <i>What does it mean:</i> <br />• The site is effected by Bushfire zoning, which means a BAL (Bushfire Attack Level) will be determined.<br />• Additionally, you will need to adhere to fire rating requirements and specific setbacks to existing structures.</p>
        					<p> <i>Implication on your build:</i> <br />The proposed structure will need to be setback a minimum of 6m from the existing dwelling. A bushfire report may be required from a license Bushfire Consultant, or a bushfire self-assessment may be requested: (<a href="https://www.rfs.nsw.gov.au/resources/publications/building-in-a-bush-fire-area/general/single-dwellingapplication">https://www.rfs.nsw.gov.au/resources/publications/building-in-a-bush-fire-area/general/single-dwellingapplication</a>- kit)</p>
        					<p> <i>How to manage:</i> <br />• Build over 6m from the dwelling/existing structures on the site.<br />• Investigate fire rated materials that may be requested by council or a certifier.<br />• Investigate licenced bushfire consultants.</p>
        			</div>
        	 </div>
        	 <div class="clear"></div>
        	 <div class="box-pdf box-13 page_break" style="padding:0;">
        		 <div style="width:100%;height:270px;position:relative;">
        				 <img src="https://api.apps1.nsw.gov.au/planning/arcgis/V1/rest/services/ePlanning/Planning_Portal_Hazard/MapServer/export?bbox=16768287.435275001%2C-4027599.5169543005%2C16772873.656972231%2C-4025117.702192101&bboxSR=102100&imageSR=102100&size=1920%2C1039&dpi=96&format=png32&transparent=true&dynamicLayers=%5B%7B%22id%22%3A229%2C%22source%22%3A%7B%22mapLayerId%22%3A229%2C%22type%22%3A%22mapLayer%22%7D%2C%22drawingInfo%22%3A%7B%22showLabels%22%3Afalse%2C%22transparency%22%3A0%7D%7D%5D&f=image" style="width: 100%; position: absolute; top: 0; left: 0; height: 270px; object-fit: cover;">
        				 <br><br>

        				 <img src="https://api.apps1.nsw.gov.au/planning/arcgis/V1/rest/services/ePlanning/Planning_Portal_Administration/MapServer/export?bbox=16768287.435275001%2C-4027599.5169543005%2C16772873.656972231%2C-4025117.702192101&bboxSR=102100&imageSR=102100&size=1920%2C1039&dpi=96&format=png32&transparent=true&dynamicLayers=%5B%7B%22id%22%3A2%2C%22source%22%3A%7B%22mapLayerId%22%3A2%2C%22type%22%3A%22mapLayer%22%7D%2C%22drawingInfo%22%3A%7B%22showLabels%22%3Afalse%2C%22transparency%22%3A0%7D%7D%5D&f=image" style="width: 100%; position: absolute; top: 0; left: 0; height: 270px; object-fit: cover;">
        				 <br><br>
        			</div>
        		</div>

        		<div class="box-pdf box-14">
         			 <div class="box-head">
         					 <h2>NEXT STEPS</h2>
         			 </div>
         			 <div class="clear"></div>
         			<div class="box-content">
         					<h3>Budget Tracker</h3>
        					<br>
        					<table>
        						<thead>
        							<th style="width:33%">Item</th>
        							<th style="width:33%">Cost Estimate</th>
        							<th style="width:33%">When is this normally needed/ When should you have decided on ordering/purchasing/</th>
        						</thead>
        						<tbody>
        							<tr>
        								<td>Structure or Kit Price</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Water Tank</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Concrete</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Kit Erection</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Plumbing</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Electricity</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Site Preparation</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Landscaping</td>
        								<td></td>
        								<td></td>
        							</tr>

        							<tr>
        								<td>Demolition</td>
        								<td></td>
        								<td></td>
        							</tr>

        							<tr>
        								<td>Site Survey, Boundary Peg Out Survey or Structural Identification Survey</td>
        								<td></td>
        								<td></td>
        							</tr>

        							<tr>
        								<td>Council Plan Set</td>
        								<td></td>
        								<td></td>
        							</tr>

        							<tr>
        								<td>Engineering Plan Set</td>
        								<td></td>
        								<td></td>
        							</tr>

        							<tr>
        								<td>Tree Removal</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Arborist Report</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Retaining Walls</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Custom engineering, i.e. slab design, retaining walls</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Sewer Peg Out</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Water Approval or Water Service Coordinator</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Council Approval or Certifier Approval Services</td>
        								<td></td>
        								<td></td>
        							</tr>

        							<tr>
        								<td>Statement of Environmental Effects</td>
        								<td></td>
        								<td></td>
        							</tr>

        							<tr>
        								<td>Council Fees</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Certifier Fees</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Geotechnical Report</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Existing slab or retaining wall inspections with an engineer</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Hydraulic stormwater design</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Ecological assessment</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Flood certificate</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Flood Impact Australia Report</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Additional council applications, i.e. s68 for toilet within structure, s138 driveway application</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Bushfire Report</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Waste Management Plan</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Basix/Nathers</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Wastewater Report</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>10.7 Certificate</td>
        								<td></td>
        								<td></td>
        							</tr>
        							<tr>
        								<td>Traffic Control Plan</td>
        								<td></td>
        								<td></td>
        							</tr>

        						</tbody>
        					</table>
         			</div>
            </div>
       </main>
    </body>
</html>
