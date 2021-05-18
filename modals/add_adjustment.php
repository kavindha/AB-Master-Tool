<div id="add_adjustment_modal" class="modal ab_modal">
<div class="modal-content">
        <div class="modal-header">          
            <h4 class="modal-title">Add Adjustment Info</h4>
        </div>
        <div class="modal-body">            
            <div class="col-sm-12 body-title">
                <div class="success">Test Info Added Succesfully</div>
                <div class="fail">Test Info Addition Failed</div>
            </div>
            <form method="post">
            <div class="field col-sm-12">                

                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Jira ID" class="" name="jira_id" >
                </div>
                <div class="input-field col-sm-12">    
                    <textarea placeholder="Adjustment" id="adjustment" name="adjustment" rows="4" cols="50"></textarea>
                </div>
                <div class="input-field col-sm-12">    
                    <textarea type="text" placeholder="Details" class="" name="details" rows="4" cols="50"></textarea>
                </div>               
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Traffic" class="" name="traffic_allocation" >
                </div>     
                <div style="display: none">
                    <input type="text" value="" id="exp_id" name="exp_id">
                </div>           
            </div>

                <div class="col-sm-12 btns">
                <button id="submit_adjustment" class="submit-btn">Add Info</button>
                <button class="close-btn">Close</button>
                </div>
                </form>      
            </div>
        </div>
    </div>
</div>