-- Original table

create table nest
	(	log_datetime datetime,	
		location text,	
		outside_temp text,	
		away_status text,	
		current_temp text,	
		current_humidity text,	
		temp_mode text,	
		target_temp text,	
		time_to_target text,	
		heat_on text,	
		ac_on text 
	);

-- added outside humidity

 create table nest
	(	log_datetime datetime,	
		location text,	
		outside_temp text,	
		outside_humidity text,	
		away_status text,	
		current_temp text,	
		current_humidity text,	
		temp_mode text,	
		target_temp text,	
		time_to_target text,	
		heat_on text,	
		ac_on text 
	);	