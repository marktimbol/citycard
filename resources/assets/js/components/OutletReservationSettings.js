// OutletReservationSettings.js

import React from 'react';
import ReactDOM from 'react-dom';

class OutletReservationSettings extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			has_reservation: app.has_reservation
		}

		this.toggleOutletReservation = this.toggleOutletReservation.bind(this)
	}


	toggleOutletReservation()
	{
		let that = this;
		let type = 'PUT';
		let has_reservation = this.state.has_reservation;
		let url = '/dashboard/outlets/' + app.outlet_id + '/activate-reservation';

		if( has_reservation )
		{
			url = '/dashboard/outlets/' + app.outlet_id + '/deactivate-reservation';
			type = 'DELETE';	
		}

		$.ajax({
			type: type,
			url: url,
			headers: {
				'X-CSRF-Token': App.csrfToken,
			},
			success: function(response) {
				that.setState({
					has_reservation: response.has_reservation
				});
			}
		})		
	}

	render()
	{
		return (
			<div className="checkbox">
				<label>
					<input type="checkbox" defaultChecked={this.state.has_reservation} onChange={this.toggleOutletReservation} /> Activate Reservation
				</label>
			</div>	
		)
	}
}

ReactDOM.render(
	<OutletReservationSettings />,
	document.getElementById('OutletReservationSettings')
);