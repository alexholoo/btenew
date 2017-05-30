{% extends "layouts/base.volt" %}

{% block main %}
<form class="form-horizontal" method="POST">
  <fieldset>
  
    <legend>Change Password</legend>

    {{ flash.output() }}
    
    <div class="form-group">
      <label class="col-md-4 control-label">Old Password</label>
      <div class="col-md-4">
        <input name="oldpass" placeholder="" class="form-control input-md" required="" type="password">
      </div>
    </div>
    
    <div class="form-group">
      <label class="col-md-4 control-label">New Password</label>
      <div class="col-md-4">
        <input name="newpass" placeholder="" class="form-control input-md" required="" type="password">
      </div>
    </div>
    
    <div class="form-group">
      <label class="col-md-4 control-label">Confirmation</label>
      <div class="col-md-4">
        <input name="newpass2" placeholder="" class="form-control input-md" required="" type="password">
        
      </div>
    </div>
    
    <div class="form-group">
      <label class="col-md-4 control-label"></label>
      <div class="col-md-8">
        <button type="submit" class="btn btn-success">Change Password</button>
      </div>
    </div>
  
  </fieldset>
</form>
{% endblock %}

