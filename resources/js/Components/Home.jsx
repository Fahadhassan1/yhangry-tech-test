import React, { useState, useEffect } from 'react';
import axios from 'axios';

const Home = () => {
    const [setMenus, setSetMenus] = useState([]);
    const [cuisines, setCuisines] = useState([]);
    const [cuisineSlug, setCuisineSlug] = useState('');
    const [perPage, setPerPage] = useState(10);
    const [guests, setGuests] = useState(1);
    const [totalPrice, setTotalPrice] = useState(0);
    const [minSpend, setMinSpend] = useState(100);

    useEffect(() => {
        fetchSetMenus();
    }, [cuisineSlug, perPage]);

    const fetchSetMenus = async () => {
        try {

            
            const response = await axios.get(`http://127.0.0.1:8000/api/set-menus`, {
                params: {
                    cuisineSlug,
                    perPage
                }
            });
            console.log(response.data);
            setSetMenus(response.data.setMenus.data || []);
            setCuisines(response.data.cuisines || []);
        } catch (error) {
            console.error('Error fetching set menus:', error);
        }
    };

    const handleGuestsChange = (e) => {
        const guests = e.target.value;
        setGuests(guests);
        calculateTotalPrice(guests);
    };

    const calculateTotalPrice = (guests) => {
        const pricePerPerson = 20; // Example price per person
        const total = pricePerPerson * guests;
        setTotalPrice(total < minSpend ? minSpend : total);
    };

    return (
        <div>
            <h1 className='text-center mt-3'>Set Menus</h1>
            <div className='text-center'>
                <label>
                    Number of Guests:
                    <input type="number" value={guests} onChange={handleGuestsChange} />
                </label>
            
            </div>
            <div className='text-center mt-3'>
                <label>
                    Cuisine:
                    <select value={cuisineSlug} onChange={(e) => setCuisineSlug(e.target.value)}>
                        <option value="">ALL </option>
                        {cuisines.map((cuisine) => (
                            <option key={cuisine.id} value={cuisine.slug}>
                                {cuisine.name}
                            </option>
                        ))}
                    </select>
                </label>
            </div>
            <div className='text-center mt-3'> <p>Total Price: ${totalPrice}</p></div>
            <div className='container'>
                <div className='mt-4 d-flex flex-wrap justify-content-between'>

                  <div className='col-md-12 row ms-5'>
                    {Array.isArray(setMenus) && setMenus.map((menu) => (
                        <div className='col-md-4' key={menu.id}>
                                <img src={menu.thumbnail} alt={menu.name} style={{ width: '100px', height: '100px' }} />
                                <div>
                                    {menu.name} - ${menu.price_per_person}
                                </div>
                        </div>
                        ))}
                    </div>
                </div>


                <div className='text-center mt-4'>            
                <button className='text-center mt-4' onClick={() => setPerPage(perPage + 20)}>Show more</button>
                </div>

            </div>

        </div>
    );
};

export default Home;