import React from 'react';
import Select, { Creatable } from 'react-select';

class Categories extends React.Component
{
    constructor(props)
    {
        super(props);

        this.state = {
            availableSubcategories: [],
            isFetchingSubcategories: false,
        }

        this.handleCategoryChange = this.handleCategoryChange.bind(this);
        this.handleSubcategoryChange = this.handleSubcategoryChange.bind(this);        
    }

    handleCategoryChange(e) {

        this.setState({ 
            isFetchingSubcategories: true 
        });
        
        this.props.handleCategoryChange(e.value);

        this.fetchSubcategories(e.value);
    }

    fetchSubcategories(selectedCategory) {
        let url = '/api/categories/'+selectedCategory+'/subcategories';

        $.get(url, function(response) {
            this.setState({
                availableSubcategories: response,
                isFetchingSubcategories: false
            });
        }.bind(this))
    }

    handleSubcategoryChange(value) {
        console.log('handleSubcategoryChange', value);

        this.props.handleSubcategoryChange(value);        
        // this.setState({
        //     selectedSubcategories: value
        // })
    }

    render()
    {
        let availableCategories = [];
        app.categories.map(category => {
            availableCategories.push({
                value: category.id,
                label: category.name
            });
        })

        let availableSubcategories = this.state.availableSubcategories.map(subcategory => {
            return {
                value: subcategory.id,
                label: subcategory.name
            }
        })

        let errors = this.props.errors;
        let categoryClass = errors.hasOwnProperty('category') ? 'form-group has-error' : 'form-group';
        let subcategoryClass = errors.hasOwnProperty('subcategories') ? 'form-group has-error' : 'form-group';

        return (
            <div className="row">
                <div className="col-md-6">
                    <div className={categoryClass}>
                        <label htmlFor="category" className="control-label">Category</label>
                        <Select
                            name="category"
                            value={this.props.selectedCategory}
                            options={availableCategories}
                            onChange={this.handleCategoryChange} />
                        { errors.hasOwnProperty('category') ?
                            <span className="help-block">{ errors['category'] }</span>
                            : <span></span>
                        }                               
                    </div>
                </div>
                <div className="col-md-6">
                    <div className={subcategoryClass}>
                        <label htmlFor="category" className="control-label">Subcategories</label>
                        <Creatable
                            name="subcategories"
                            value={this.props.selectedSubcategories}
                            options={availableSubcategories}
                            isLoading={this.state.isFetchingSubcategories}
                            multi={true}
                            joinValues
                            onChange={this.handleSubcategoryChange} />
                        { errors.hasOwnProperty('subcategories') ?
                            <span className="help-block">{ errors['subcategories'] }</span>
                            : <span></span>
                        }                              
                    </div>
                </div>
            </div>
        )
    }
}

export default Categories;
