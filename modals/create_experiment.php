<div id="create_experiment_modal" class="modal ab_modal">
<div class="modal-content">
        <div class="modal-header">          
            <h4 class="modal-title">Create Experiment</h4>
        </div>
        <div class="modal-body">            
            <div class="col-sm-12 body-title">
                <div class="success">Experiment Created Succesfully</div>
                <div class="fail">Experiment Creation Failed</div>
            </div>
            
            <div class="experiment_data">
            <form method="post">
            <div class="field col-sm-12">                
            <label> Experiment Info</label>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Experiment Name" class="" name="experiment_name" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Experiment Key" class="" name="key">
                </div>                
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Traffic" class="" name="traffic_distribution" >
                </div>          
            </div>

                <div class="col-sm-12 btns">
                <button id="submit_exp" class="submit-btn">Create Experiment</button>
                <button class="close-btn">Close</button>
                </div>
                </form>
            </div>
            <div class="variation_data">
            <form method="post">
            <div class="field col-sm-12">                
            <h4>Variantion Info</h4>   
            <div style="display: none">
                    <input type="text" value="" class="new_exp_id" name="exp_id">
            </div>        
            <div class="variation_info">
            <label>Variation 1</label>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Caption" class="caption" name="caption" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Key" class="key" name="key">
                </div>                
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Traffic" class="traffic_distribution" name="traffic_distribution" >
                </div> 
            </div>
            <div class="variation_info">
            <label>Variation 2</label>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Caption" class="caption" name="caption" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Key" class="key" name="key">
                </div>                
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Traffic" class="traffic_distribution" name="traffic_distribution" >
                </div> 
            </div>    
                    
            </div>
                <img src="images/add.png" id="add-btn"/>     
                <div class="col-sm-12 btns">
                <button id="submit_variations" class="submit-btn">Create Variations</button>                
                </div>
                </form>
            </div>
            
            <div class="event_data">
            <form method="post">
            <div class="field col-sm-12">                
            <h4>Event Info</h4>   
            <div style="display: none">
                    <input type="text" value="" class="new_exp_id" name="exp_id">
            </div>        
            <div class="event_info">
            <label>Event 1</label>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Caption" class="caption" name="caption" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Key" class="key" name="key">
                </div>            
            </div>
            <div class="event_info">
            <label>Event 2</label>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Caption" class="caption" name="caption" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Key" class="key" name="key">
                </div>               
            </div>    
                    
            </div>
                <!-- <img src="images/add.png" id="add-event-btn"/>      -->
                <div class="col-sm-12 btns">
                <button id="submit_events" class="submit-btn">Create Events</button>                
                </div>
                </form>
            </div>

            <div class="exp_info_data">
            <h4>Add AB Test Info</h4>   
            <form method="post"> 
                <div style="display: none">
                    <input type="text" value="" class="new_exp_id" name="experiment_id">
                </div>           
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Element" class="" name="element"  >
                </div>
                <div class="input-field col-sm-12">    
                    <textarea placeholder="Description"  name="decription" rows="4" cols="50"></textarea>
                </div>
                <div class="input-field col-sm-12">    
                    <textarea type="text" placeholder="Details" class="" name="details"  rows="4" cols="50"></textarea>
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Objective" class="" name="objective" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Focce Link" class="" name="force_link">
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Justification" class="" name="justification" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Jira ID" class="" name="jira_id" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Traffic" class="" name="traffic_allocation" >
                </div>
                <div class="col-sm-12 btns">
                <button id="submit_ab_info_new" class="submit-btn">Add Info</button>               
                </div>
                </form>      
                </div>

               
            </div>      
            </div>
        </div>
    </div>
</div>