// ExploreMerchants.js

import React from 'react';
import ReactDOM from 'react-dom';
import ExploreMerchant from './ExploreMerchant';

class ExploreMerchants extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			outlets: app.outlets
		}
	}

	render()
	{
		let outlets = this.state.outlets.data.map(outlet => {
			return (
				<ExploreMerchant outlet={outlet} key={outlet.id} />
			)
		})

		return (
			<div className="Explore__content--container">
				{outlets}
			</div>
		)
	}
}

ReactDOM.render(
	<ExploreMerchants />,
	document.getElementById('ExploreMerchants')
);