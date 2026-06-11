<!DOCTYPE html>
<html lang="en">
<head>
<title>Maruti Admin</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="<?php echo base_url()?>template_back/css/bootstrap.min.css" />
<link rel="stylesheet" href="<?php echo base_url()?>template_back/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="<?php echo base_url()?>template_back/css/colorpicker.css" />
<link rel="stylesheet" href="<?php echo base_url()?>template_back/css/datepicker.css" />
<link rel="stylesheet" href="<?php echo base_url()?>template_back/css/uniform.css" />
<link rel="stylesheet" href="<?php echo base_url()?>template_back/css/select2.css" />
<link rel="stylesheet" href="<?php echo base_url()?>template_back/css/maruti-style.css" />
<link rel="stylesheet" href="<?php echo base_url()?>template_back/css/maruti-media.css" class="skin-color" />
</head>
<body>



<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#" class="tip-bottom">Form elements</a> <a href="#" class="current">Common elements</a> </div>
    <h1>Common Form Elements</h1>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
   
     
    </div><hr>
    <div class="row-fluid">
      <div class="span6">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
            <h5>Form Elements</h5>
          </div>
          <div class="widget-content nopadding">
            <form class="form-horizontal">
             
              
              <div class="control-group">
                <label class="control-label">Date picker (dd-mm)</label>
                <div class="controls">
                  <input type="text" data-date="01-02-2013" data-date-format="dd-mm-yyyy" value="01-02-2013" class="datepicker span11">
                  <span class="help-block">Date with Formate of  (dd-mm-yy)</span> </div>
              </div>
              <div class="control-group">
                <label class="control-label">Date Picker (mm-dd)</label>
                <div class="controls">
                  <div  data-date="12-02-2012" class="input-append date datepicker">
                    <input type="text" value="12-02-2012"  data-date-format="mm-dd-yyyy" class="span11" >
                    <span class="add-on"><i class="icon-th"></i></span> </div>
                </div>
              </div>
            
              <div class="form-actions">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="submit" class="btn btn-primary">Reset</button>
                <button type="submit" class="btn btn-info">Edit</button>
                <button type="submit" class="btn btn-danger">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
     
    </div>
  </div>
</div>
</div>
<div class="row-fluid">
  <div id="footer" class="span12"> 2012 &copy; Marutii Admin. Brought to you by <a href="http://themedesigner.in">Themedesigner.in</a> </div>
</div>
<script src="<?php echo base_url()?>template_back/js/jquery.min.js"></script> 
<script src="<?php echo base_url()?>template_back/js/jquery.ui.custom.js"></script> 
<script src="<?php echo base_url()?>template_back/js/bootstrap.min.js"></script> 
<script src="<?php echo base_url()?>template_back/js/bootstrap-colorpicker.js"></script> 
<script src="<?php echo base_url()?>template_back/js/bootstrap-datepicker.js"></script> 
<script src="<?php echo base_url()?>template_back/js/jquery.uniform.js"></script> 
<script src="<?php echo base_url()?>template_back/js/select2.min.js"></script> 
<script src="<?php echo base_url()?>template_back/js/maruti.js"></script> 
<script src="<?php echo base_url()?>template_back/js/maruti.form_common.js"></script>
</body>
</html>
