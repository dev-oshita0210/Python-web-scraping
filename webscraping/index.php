<!DOCTYPE html>
<html>
<style>
  table {
    border-collapse: collapse;
  }

  th, td, tr {
    border: 1px solid black;
    padding: 8px;
  }
</style>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <title>Scraping for maxaroma.com</title>
</head>

<body>
    <div class="container mt-3">
        <div class="row mt-3">
            <div class="col-md-3 p-3 mt-5">
                <h2>Scraping Info</h2>
                <div class="mb-3 mt-3">
                    <label for="email">Type:</label>
                    <div class="dropdown">
                        <Select v-model="" id='type'>
                            <Option value="FRAGRANCE" key="">FRAGRANCE</Option>
                            <Option value="SKINCARE" key="">SKINCARE</Option>
                            <Option value="POCKET PERFUME" key="">POCKET PERFUME</Option>
                            <Option value="CANDLES" key="">CANDLES</Option>
                        </Select>
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label for="email">Category:</label>
                    <div class="dropdown">
                        <Select v-model="" id='category'>
                            <Option value="https://www.maxaroma.com/fragrancemen-fragrances/p4u/cid-3/view" key="">Men
                            </Option>
                            <Option value="https://www.maxaroma.com/fragranceunisex/p4u/cid-4/view" key="">Unisex
                            </Option>
                            <Option value="https://www.maxaroma.com/women/scid/5" key="">Women</Option>
                            <Option value="https://www.maxaroma.com/fragrances/niche-perfumes/scid/2" key="">Niche
                                Fragrances</Option>
                        </Select>
                    </div>
                </div>
                <button class="btn btn-primary">Scrape</button>
            </div>
            <div class="col-md-9 mt-5 p-3">
                <h2>Result</h2>
                <table id="result" class="table table-bordered">
                    <tr>
                        <th>No</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Price</th>
                        <th>SKU</th>
                        <th>Description</th>
                        <th>More Detail</th>
                    </tr>
                </table>

            </div>
        </div>

    </div>
</body>

<script>
    $(document).ready(function () {
        $("button").click(function () {

            $.ajax({
                url: "process.php",
                type: "post",
                data: {
                    type: $('#type').find(":selected").val(),
                    category: $('#category').find(":selected").val(),
                },
                success: function (response) {
                    // alert(response);
                    data = JSON.parse(response);
                    data.forEach((item, index) => {

                        var row = document.createElement("tr");

                        var no = document.createElement("td");
                        no.textContent = index + 1;
                        row.appendChild(no);

                        var category = document.createElement("td");
                        category.textContent = item.category;
                        row.appendChild(category);

                        var brand = document.createElement("td");
                        brand.textContent = item.brand;
                        row.appendChild(brand);

                        var name = document.createElement("td");
                        name.textContent = item.name;
                        row.appendChild(name);

                        var link = document.createElement("td");
                        link.textContent = item.url;
                        row.appendChild(link);

                        var price = document.createElement("td");
                        price.textContent = item.price;
                        row.appendChild(price);

                        var sku = document.createElement("td");
                        sku.textContent = item.sku;
                        row.appendChild(sku);

                        var description = document.createElement("td");
                        description.textContent = item.description;
                        row.appendChild(description);

                        var more_detail = document.createElement("td");
                        more_detail.textContent = item.more_detail;
                        row.appendChild(more_detail);

                        var table = document.getElementById("result");
                        table.appendChild(row);
                    })
                },
                error: function (xhr) {
                    alert("xhr: ");
                }
            });
        });
    });
</script>

</html>