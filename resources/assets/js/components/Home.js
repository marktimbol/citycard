// home.js
import React from 'react';
import ReactDOM from 'react-dom';
import RegisterUser from './RegisterUser';
import LoginUser from './LoginUser';

class Home extends React.Component
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
			<main>
				<article className="Flex Flex--center">
					<div className="Column-6 PhoneMockup--container">
						<div className="PhoneMockup">
							<div className="PhoneMockup__slides">
								<img src="/images/iphone-slide.jpg" alt="" title="" className="PhoneMockup__slide1" />
								<img src="/images/iphone-slide2.jpg" alt="" title="" className="PhoneMockup__slide2" />
								<img src="/images/iphone-slide3.jpg" alt="" title="" className="PhoneMockup__slide3" />
							</div>
						</div>
					</div>
					<div className="Column-6 max-350">
						<div className="Authentication">
							{ this.state.showRegister ? <RegisterUser showLogin={this.showLogin} /> : <span></span> }
							{ this.state.showLogin ? <LoginUser showRegister={this.showRegister} /> : <span></span> }
						</div>
					</div>					
				</article>

				<div className="Flex Flex--center Footer--container">
					<div className="Column-6">
						<footer>
							<ul>
								<li><a href="/posts">Deals</a></li>
								<li><a href="/events">Events</a></li>
								<li><a href="/directory">Directory</a></li>
							</ul>
							<ul>
								<li><a href="/merchants">Merchants</a></li>
								<li><a href="/about">About Us</a></li>
								<li><a href="/support">Support</a></li>
							</ul>
						</footer>
					</div>

					<div className="Column-6">
						<div className="Download-app">
							<ul>
								<li>
									<a href="#">
										<img src="/images/app-store.png" alt="Download on the App Store" title="Download on the App Store" className="img-responsive" />
									</a>
								</li>
								<li>
									<a href="#">
										<img src="/images/google-play.png" alt="Get it on the Google Play" title="Get it on the Google Play" className="img-responsive" />
									</a>
								</li>								
							</ul>
						</div>
					</div>
				</div>

				<footer>
					<ul>
						<li className="copyright text-center">
							&copy; 2017 City Card
						</li>
					</ul>
				</footer>				
			</main>
		)
	}
}

ReactDOM.render(
	<Home />,
	document.getElementById('Home')
)