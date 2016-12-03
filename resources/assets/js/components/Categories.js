import React from 'react';
import Select, { Creatable } from 'react-select';

class Categories extends React.Component
{
    constructor(props)
    {
        super(props);

        this.state = {
            selectedCategory: '',
            selectedSubcategories: [],
            availableSubcategories: [],
            isFetchingSubcategories: false,
        }

        this.handleCategoryChange = this.handleCategoryChange.bind(this);
        this.handleSubcategoryChange = this.handleSubcategoryChange.bind(this);        
    }

    handleCategoryChange(e) {
        this.setState({ 
            selectedCategory: e.value, 
            isFetchingSubcategories: true 
        });
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
        this.setState({
            selectedSubcategories: value
        })
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

        return (
            <div className="row">
                <div className="col-md-6">
                    <div className="form-group">
                        <label htmlFor="category">Category</label>
                        <Select
                            name="category"
                            value={this.state.selectedCategory}
                            options={availableCategories}
                            onChange={this.handleCategoryChange} />
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="form-group">
                        <label htmlFor="category">Subcategories</label>
                        <Creatable
                            name="subcategories"
                            value={this.state.selectedSubcategories}
                            options={availableSubcategories}
                            isLoading={this.state.isFetchingSubcategories}
                            multi={true}
                            joinValues
                            onChange={this.handleSubcategoryChange} />
                    </div>
                </div>
            </div>
        )
    }
}

export default Categories;
