// vend.js

import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

class Vend extends React.Component
{
	componentDidMount() {
		let endpoint = 'https://secure.vendhq.com/connect?response_type=code&client_id=Zi443rp53eYjLXUkKaygntis5TDbHVAW&redirect_uri=http://citycard.me&state=';

		axios.get(endpoint)
			.then(function(response) {
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