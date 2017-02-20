// vend.js

import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

class Vend extends React.Component
{
	componentDidMount() {
		axios.get('https://secure.vendhq.com/connect', {
			params: {			
				'response_type': 'code',
				'client_id': 'Zi443rp53eYjLXUkKaygntis5TDbHVAW',
				'redirect_uri': 'http://citycard.me',
				'state': ''
			},
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'Access-Control-Allow-Origin': '*',
				'Access-Control-Allow-Methods': 'PUT, GET, POST, DELETE, OPTIONS'
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