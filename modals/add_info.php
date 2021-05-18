<div id="add_info_modal" class="modal ab_modal">
<div class="modal-content">
        <div class="modal-header">          
            <h4 class="modal-title">Add AB Test Info</h4>
        </div>
        <div class="modal-body">            
            <div class="col-sm-12 body-title">
                <div class="success">Test Info Added Succesfully</div>
                <div class="fail">Test Info Addition Failed</div>
            </div>
            <form method="post">
            <div class="field col-sm-12">
                
                <div class="input-field col-sm-12">
                <div>
                    <select id="experiment_id" class="input-name" name="experiment_id" data-default="Select an Experiment ID">
                        <option value="">Select an Experiment</option>
                        <?php
                            $experiments  = AB_Tests::get_experiment_ids();
                            foreach($experiments as $exp){                              
                                    echo "<option value='".$exp['id']."'>".$exp['name']."</option>";                                
                            }
                        ?>
                    </select>
                </div>
                </div>

                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Element" class="" name="element" id="element" >
                </div>
                <div class="input-field col-sm-12">    
                    <textarea placeholder="Description" id="description" name="decription" rows="4" cols="50"></textarea>
                </div>
                <div class="input-field col-sm-12">    
                    <textarea type="text" placeholder="Details" class="" name="details" id="details" rows="4" cols="50"></textarea>
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Objective" class="" name="objective" id="objective" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Focce Link" class="" name="force_link" id="force_link" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Justification" class="" name="justification" id="justification" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Jira ID" class="" name="jira_id" id="jira_id" >
                </div>
                <div class="input-field col-sm-12">    
                    <input type="text" placeholder="Traffic" class="" name="traffic_allocation" id="traffic" >
                </div>
                <div class="input-field col-sm-12">
                    <label>Start Date :</label><br/>  
                    <input placeholder="Start Date" type="date" id="start_date" name="start_date">
                </div>
                </div>

                <div class="col-sm-12 btns">
                <button id="submit_ab_info" class="submit-btn">Add Info</button>
                <button id="ab_info_close" class="close-btn">Close</button>
                </div>
                </form>      
            </div>
        </div>
    </div>
</div>