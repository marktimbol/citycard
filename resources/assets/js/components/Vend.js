// vend.js

import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

class Vend extends React.Component
{
	componentDidMount() {
		let endpoint = 'https://arksolutions.vendhq.com/api/products/06bf537b-c7cd-11e6-ff13-f74d7d458d45';

		axios.get(endpoint, {
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'Authorization': 'Bearer CjOC4V9CKof2GwxORLlcw:eOada7qskzRU9xdCSv',
				'Access-Control-Allow-Origin': '*'
			}
		}).then(function(response) {
			console.log('response', response);
		}).catch(function(error) {
			console.log('error', error)
		})
	}

	render()
	{
		return (
			<p>Hi from Vend</p>
		)
	}
}

ReactDOM.render(
	<Vend />,
	document.getElementById('Vend')
);