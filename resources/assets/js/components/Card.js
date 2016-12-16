import React from 'react';

class Card extends React.Component
{
	itemDescription() {
		let item = this.props.item;

		return {
			__html: item.desc
		}
	}

	render()
	{
		let item = this.props.item;
		let logo = 'http://placehold.it/28x28';
		let featuredImage = 'http://placehold.it/600x500';
		
		if( item.merchant.logo != null )
		{
			logo = 'https://s3-us-west-2.amazonaws.com/citycarddev/' + item.merchant.logo;
		}

		if (item.photos.length > 0 ) {
			featuredImage = 'https://s3-us-west-2.amazonaws.com/citycarddev/' + item.photos[0].url;
		}

		return (
			<div className="Card">
				<div className="Card__header">
					<div className="Flex Flex--center">
						<img src={logo} alt={item.outlets[0].name} title={item.outlets[0].name} className="img-circle Card__logo" width="30" height="30" />
						<h4 className="Card__title">
							<a href="#">{item.outlets[0].name}</a>
						</h4>
					</div>
					<div>
						<span className="timeago">
							{ item.created_at }
						</span>
					</div>
				</div>
				<div className="Card__image">
					<img src={ featuredImage } alt={ item.title } title={ item.title } className="img-responsive" />
				</div>
				<div className="Card__description" dangerouslySetInnerHTML={this.itemDescription()}></div>
			</div>
		)
	}
}

export default Card;