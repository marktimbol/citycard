import React from 'react';

class ProgressiveMedia extends React.Component
{
	constructor(props) {
		super(props);
		
		this.state = {
			paddingBottom: '',
		}
	}

	componentDidMount() {
		let placeholder = this.refs.placeholder;
		let small = this.refs.small

		// 1: load small image and show it
		let img = new Image();
		img.src = small.src;
		img.onload = function() {
			small.classList.add('loaded');
		};

		// 2: load large image
		let imgLarge = new Image();
		imgLarge.src = placeholder.dataset.large;

		let that = this;
		imgLarge.onload = function() {
			imgLarge.classList.add('loaded');

			let aspectRatioFill = that.refs.aspectRatioFill;
			let percentage = (imgLarge.naturalHeight / imgLarge.naturalWidth) * 100;

			that.setState({
				paddingBottom: percentage + '%'
			});			
		};

		placeholder.appendChild(imgLarge);		
	}

	render()
	{
		let small_image = '/images/blurred-image.jpeg';

		return (
			<div ref="placeholder" className="placeholder" data-large={this.props.featuredImage}>
				<img ref="small" src={small_image} className="img-small" /> 
				<div ref="aspectRatioFill" className="aspect-ratio-fill" style={{ paddingBottom: this.state.paddingBottom }}></div> 
			</div>	
		)
	}
}

export default ProgressiveMedia;