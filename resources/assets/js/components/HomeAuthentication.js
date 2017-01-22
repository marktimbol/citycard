// HomeAuthentication.js
import React from 'react';
import ReactDOM from 'react-dom';
import RegisterUser from './RegisterUser';
import LoginUser from './LoginUser';

class HomeAuthentication extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			showRegister: true,
			showLogin: false,
		}

		this.showLogin = this.showLogin.bind(this);
		this.showRegister = this.showRegister.bind(this);
	}

	showLogin() {
		this.setState({
			showRegister: false,
			showLogin: true
		})
	}

	showRegister() {
		this.setState({
			showRegister: true,
			showLogin: false
		})
	}	

	render()
	{
		return (
			<div className="Authentication">
				{ this.state.showRegister ? <RegisterUser showLogin={this.showLogin} /> : <span></span> }
				{ this.state.showLogin ? <LoginUser showRegister={this.showRegister} /> : <span></span> }
			</div>
		)
	}
}

ReactDOM.render(
	<HomeAuthentication />,
	document.getElementById('HomeAuthentication')
)