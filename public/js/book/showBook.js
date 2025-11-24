import { formatMoney } from "./getDataBook.js";
import { generateStarRating } from "../common.js";


let page = parseInt(localStorage.getItem("currentPage")) || 1;
let pageSize = parseInt(localStorage.getItem("pageSize")) || 10;
localStorage.setItem("pageSize", pageSize);

function formatJSONListFilter(key) {
  let list = JSON.parse(localStorage.getItem(key) || "[]");
  return Array.isArray(list) && list.length > 0 ? list.join(",") : "";
}


function formatNumber(numberString) {
  return numberString.replace(/\D/g, '');
}

function scrollToBook() {
  document.querySelector('.book-category').scrollIntoView(
    {
      behavior: "smooth",
      block: "start"
    }
  );
}

document.addEventListener("DOMContentLoaded", function () {
  // const categoryType = document.getElementById("type-category");
  const homeView = document.getElementById('home-view');
  const productListView = document.getElementById('product-list-view');
  const sortType = document.getElementById("sort-combobox");
  const pageSizeSelect = document.getElementById("page-show-by");
  const minPrice = formatNumber(document.getElementById('min-price').innerText);
  const maxPrice = formatNumber(document.getElementById('max-price').innerText);

  // Hàm render sách vào một container cụ thể
  function renderBooksToContainer(books, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = "";

    if (!books || books.length === 0) {
      container.innerHTML = `<p class="no-books">Chưa có sách để hiển thị.</p>`;
      return;
    }

    let bookHTML = '';
    books.forEach((product) => {
      let sellingPrice = Math.round(product["giaBan"] * (1 - product["phanTramGiamGia"] / 100));
      let priceHTML = '';
      let saleBadgeHTML = '';
      if (product["phanTramGiamGia"] > 0) {
        priceHTML = `
            <div class="book-category__item-price-original-wrapper">
                <span class="book-category__item-price-sale">${formatMoney(sellingPrice)}</span>
                <span class="book-category__item-price-original">${formatMoney(product["giaBan"])}</span>
            </div>`;
        saleBadgeHTML = `<span class="badge-sale">-${product["phanTramGiamGia"]}%</span>`;
      } else {
        priceHTML = `${formatMoney(product["giaBan"])}`;
      }
        bookHTML += `

        <div class="product-card-modern" onclick="showDetailProduct(${product["maSach"]})">
                        ${saleBadgeHTML}
                        <div class="card-img">
                            <img src="public/uploads/books/${product["hinhAnh"]}" alt="Book">
                            <div class="card-actions">
                                <button class="action-btn" title="Xem nhanh" onclick="showDetailProduct(${product["maSach"]})"><i class="fa-regular fa-eye"></i></button>
                                <button class="action-btn" title="Thêm vào giỏ"><i
                                        class="fa-solid fa-cart-plus"></i></button>
                            </div>
                        </div>
                        <div class="card-info">
                            <h3 class="card-title">${product["tenSach"]}</h3>
                            <div class="card-rating">
                                <span class="show-detail-product__rating">
                                    ${generateStarRating(product["trungBinhDanhGia"])}
                                    <div class="show-detail-product__rating--number"
                                        style="color: #ccc; font-size: 1.5rem">(${product["tongDanhGia"]} đánh giá)</span>
                                </div>
                            </div>
                            <div class="card-price">
                                ${priceHTML}
                            </div>
                        </div>
                    </div>
      `;
    });
    container.innerHTML = bookHTML;
  }

  // Hàm render tác giả
  function renderAuthorsToContainer(authors, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = "";

    if (!authors || authors.length === 0) {
      container.innerHTML = `<p class="no-authors">Chưa có tác giả để hiển thị.</p>`;
      return;
    }

    let authorHTML = '';
    authors.forEach((author) => {
      authorHTML += `
        <div class="author-item" onclick="location.href='index.php?page=author&id=${author.maTacGia}'">
          <img src="public/uploads/authors/${author.hinhAnh}" alt="${author.tenTacGia}" class="author-item__image">
          <p class="author-item__name">${author.tenTacGia}</p>
        </div>
      `;
    });
    container.innerHTML = authorHTML;
  }


  // Hàm tải nội dung cho trang chủ
  async function loadHomePageContent() {
    try {
      showLoading();
      console.log("Bắt đầu tải nội dung trang chủ...");

      // Sử dụng Promise.all để tải song song
      const [
        featuredData,
        saleData,
        newData,
        ratedData,
        discountData,
        authorData
      ] = await Promise.all([
        fetch('api/home/feature_books.php?limit=5').then(res => {
          console.log("API feature_books response:", res);
          return res.json();
        }),
        fetch('api/home/sale_books.php?limit=5').then(res => {
          console.log("API sale_books response:", res);
          return res.json();
        }),
        fetch('api/home/new_books.php?llimit=5').then(res => {
          console.log("API new_books response:", res);
          return res.json();
        }),
        fetch('api/home/rate_books.php?limit=5').then(res => {
          console.log("API rate_books response:", res);
          return res.json();
        }),
        fetch('api/home/discount_books.php?limit=5').then(res => {
          console.log("API discount_books response:", res);
          return res.json();
        }),
        fetch('api/home/feature_authors.php?limit=5').then(res => {
          console.log("API feature_authors response:", res);
          return res.json();
        })
      ]);

      // Render các section sách
      renderBooksToContainer(featuredData, 'featured-books-list');
      renderBooksToContainer(saleData, 'sale-books-list');
      renderBooksToContainer(newData, 'new-books-list');
      renderBooksToContainer(ratedData, 'top-rated-books-list');
      renderBooksToContainer(discountData, 'discount-books-list');

      // Render section tác giả
      renderAuthorsToContainer(authorData, 'featured-authors-list');


      hideLoading();
    } catch (error) {
      console.error("Lỗi khi tải nội dung trang chủ:", error);
      // Có thể hiển thị thông báo lỗi cho người dùng ở đây
      hideLoading();
    }
  }

  // Hàm lấy danh sách sách từ API
  async function fetchBooks() {
    try {
      showLoading();
      // const categoryValue = categoryType !== "all-category" ? categoryType : "";
      const categoryValue = localStorage.getItem('selectedCategory') != ('all-category' || 'home-view') ? localStorage.getItem('selectedCategory') : '';
      const sortValue = sortType.value !== "base" ? sortType.value : "";
      const pageSizeValue = parseInt(pageSizeSelect.value);

      // Dữ liệu từ LocalStorage
      const page = localStorage.getItem("currentPage") || 1;
      const minPrice = localStorage.getItem("minPrice") || 0;
      const maxPrice = localStorage.getItem("maxPrice") || 2000000;

      const authorIDList = formatJSONListFilter("selectedAuthors");
      const coverIDList = formatJSONListFilter("selectedCovers");
      const publisherIDList = formatJSONListFilter("selectedPublishers");

      // Lưu lại pageSize vào localStorage
      localStorage.setItem("pageSize", pageSizeValue);

      // Khởi tạo danh sách tham số
      let queryParams = [];

      if (categoryValue) queryParams.push(`cateId=${categoryValue}`);
      if (sortValue) queryParams.push(`orderBy=${sortValue}`);
      if (pageSizeValue) queryParams.push(`pageSize=${pageSizeValue}`);
      if (page) queryParams.push(`page=${page}`);
      if (authorIDList) queryParams.push(`authorId=${authorIDList}`);
      if (coverIDList) queryParams.push(`coverType=${coverIDList}`);
      if (publisherIDList) queryParams.push(`publisherId=${publisherIDList}`);
      if (minPrice) queryParams.push(`minPrice=${minPrice}`);
      if (maxPrice) queryParams.push(`maxPrice=${maxPrice}`);

      queryParams.push('bookStatus=Đang bán');
      // Tạo URL mới (KHÔNG encode dấu `,`)
      let newUrl = `${window.location.origin}${window.location.pathname}?${queryParams.join("&")}`;

      // Cập nhật URL mà không load lại trang
      history.replaceState(null, document.title, newUrl);
      let apiUrl = `api/books/get.php?${queryParams.join("&")}`;
      // console.log(apiUrl);

      let response = await fetch(apiUrl);
      if (!response.ok) throw new Error("Lỗi khi lấy dữ liệu! HTTP Status: " + response.status);
      showLoading();
      let data = await response.json();
      // console.log("Dữ liệu API nhận được:", data);
      hideLoading();
      updateBookToMain(data.books);
      updatePagination(data.totalBooks);
    } catch (error) {
      document.getElementById("book-list").innerHTML = `<p class="error-message">Lỗi khi tải sách!</p>`;
    }
    hideLoading();
  }

  // Cập nhật danh sách sách vào giao diện
  function updateBookToMain(allproduct = []) {
    const bookList = document.getElementById("book-list");
    bookList.innerHTML = "";

    if (allproduct.length === 0) {
      bookList.innerHTML = `<p class="no-books">Không tìm thấy sách!</p>`;
      return;
    }
    allproduct.forEach((product) => {
      let statusProduct = product["status"];
      let className =
        statusProduct === "Đang bán"
          ? "book-category__item-status--true"
          : "book-category__item-status--false";
      let sellingPrice = Math.round(product["sellingPrice"] * (1 - product["discount"] / 100));
      let priceHTML = '';
      if (product["discount"] > 0) {
        priceHTML = `
            <div class="book-category__item-price-sale">${formatMoney(sellingPrice)}</div>
            <div class="book-category__item-price-original-wrapper">
                <span class="book-category__item-price-original">${formatMoney(product["sellingPrice"])}</span>
                <span class="book-category__item-price-percentage">  ${product["discount"]}%</span>
            </div>    `;
      } else {
        priceHTML = `${formatMoney(product["sellingPrice"])}`;
      }

      bookList.innerHTML += `
        <div class="book-category__item" onclick="showDetailProduct(${product["id"]})">
            <img src="public/uploads/books/${product["image"]}" class="book-category__item-image"></img>
            <div class="book-category__item-name">${product["name"]}</div>

            
            <div class="book-category__item-price">${priceHTML}</div>
            <div class="book-category__item-add-to-cart margin-top-small">
                <i class="fa-solid fa-cart-plus book-category__item-button-icon"></i>
                <span class="book-category__item-button-text">Thêm vào giỏ hàng</span>
            </div>
        </div>
      `;
    });
  }

  // Cập nhật giao diện phân trang
  function updatePagination(totalBooks) {
    let maxPage = Math.ceil(totalBooks / pageSize);
    let paginationContainer = document.getElementById("pagination");
    paginationContainer.innerHTML = "";

    if (maxPage <= 1) return;

    let prevButton = document.createElement("button");
    prevButton.textContent = "←";
    prevButton.classList.add("pagination-btn");
    if (page === 1) prevButton.disabled = true;
    prevButton.addEventListener("click", function () {
      if (page > 1) {
        page--;
        localStorage.setItem("currentPage", page);
        fetchBooks();
      }
    });
    paginationContainer.appendChild(prevButton);

    if (page > 2) {
      let firstPage = document.createElement("button");
      firstPage.textContent = "1";
      firstPage.classList.add("pagination-btn");
      firstPage.addEventListener("click", function () {
        page = 1;
        localStorage.setItem("currentPage", page);
        fetchBooks();
      });
      paginationContainer.appendChild(firstPage);
    }

    if (page > 3) {
      let dots = document.createElement("span");
      dots.textContent = "...";
      dots.classList.add("pagination-dots");
      paginationContainer.appendChild(dots);
    }

    for (let i = Math.max(1, page - 1); i <= Math.min(maxPage, page + 1); i++) {
      let button = document.createElement("button");
      button.textContent = i;
      button.classList.add("pagination-btn");
      if (i === page) button.classList.add("active");
      button.addEventListener("click", function () {
        if (i !== page) {
          page = i;
          localStorage.setItem("currentPage", page);
          fetchBooks();
        }
      });
      paginationContainer.appendChild(button);
    }

    if (page < maxPage - 2) {
      let dots = document.createElement("span");
      dots.textContent = " . . . ";
      dots.classList.add("pagination-dots");
      paginationContainer.appendChild(dots);
    }

    if (page < maxPage - 1) {
      let lastPage = document.createElement("button");
      lastPage.textContent = maxPage;
      lastPage.classList.add("pagination-btn");
      lastPage.addEventListener("click", function () {
        page = maxPage;
        localStorage.setItem("currentPage", page);
        fetchBooks();
      });
      paginationContainer.appendChild(lastPage);
    }

    let nextButton = document.createElement("button");
    nextButton.textContent = "→";
    nextButton.classList.add("pagination-btn");
    if (page === maxPage) nextButton.disabled = true;
    nextButton.addEventListener("click", function () {
      if (page < maxPage) {
        page++;
        localStorage.setItem("currentPage", page);
        fetchBooks();
      }
    });
    paginationContainer.appendChild(nextButton);
  }

  // const categoryList = document.querySelectorAll('.menu-item');
  // if (categoryList) {
  //   categoryList.forEach(category => {
  //     category.addEventListener('click', function (event) {
  //       event.preventDefault(); // Ngăn hành vi mặc định của thẻ <a>

  //       const categoryId = category.dataset.id;

  //       if (categoryId === 'all-category') {
  //         // Nếu nhấp vào "Tất cả", hiển thị trang chủ
  //         homeView.classList.remove('hide-item');
  //         productListView.classList.add('hide-item');
  //       } else {
  //         // Nếu nhấp vào một thể loại cụ thể, hiển thị danh sách sản phẩm
  //         homeView.classList.add('hide-item');
  //         productListView.classList.remove('hide-item');

  //         // Chạy logic tìm nạp sách hiện có của bạn
  //         page = 1;
  //         localStorage.setItem('currentPage', page);
  //         localStorage.setItem('selectedCategory', categoryId);
  //         fetchBooks();
  //         scrollToBook();
  //       }
  //     });
  //   });
  // }

  const categoryList = document.querySelectorAll('.menu-item');
  if (categoryList) {
    categoryList.forEach(category => {
      category.addEventListener('click', function (event) {
        event.preventDefault(); // Ngăn hành vi mặc định của thẻ <a>
        const categoryId = category.dataset.id;
        const homeView = document.getElementById('home-view');
        const productListView = document.getElementById('product-list-view');

        if (categoryId === 'home-view') {
          // Khi click vào "Trang chủ"
          homeView.classList.remove('hide-item');
          productListView.classList.add('hide-item');
          localStorage.removeItem('selectedCategory'); // Xóa thể loại đã chọn
          // Có thể gọi lại loadHomePageContent() nếu cần cập nhật lại
          loadHomePageContent();
        } else {
          // Khi click vào một thể loại khác hoặc "Tất cả"
          homeView.classList.add('hide-item');
          productListView.classList.remove('hide-item');

          // Chạy logic tìm nạp sách cho thể loại đã chọn
          page = 1;
          localStorage.setItem('currentPage', page);
          localStorage.setItem('selectedCategory', categoryId);
          fetchBooks();
          scrollToBook();
        }
      });
    });
  } else {
    homeView.classList.remove('hide-item');
    productListView.classList.add('hide-item');
  }



  sortType.addEventListener("change", () => {
    page = 1;
    localStorage.setItem("currentPage", page);
    fetchBooks();
  });

  pageSizeSelect.addEventListener("change", () => {
    page = 1;
    localStorage.setItem("currentPage", page);
    fetchBooks();
  });

  document.querySelectorAll('.list-author-content').forEach(boxItem => {
    boxItem.addEventListener("change", function (event) {
      if (event.target.classList.contains("filter-group__checkbox")) {
        fetchBooks();
      }
    });
  });

  document.querySelectorAll('.list-publisher-content').forEach(boxItem => {
    boxItem.addEventListener("change", function (event) {
      if (event.target.classList.contains("filter-group__checkbox")) {
        fetchBooks();
      }
    });
  });

  document.querySelectorAll('.list-cover-content').forEach(boxItem => {
    boxItem.addEventListener("change", function (event) {
      if (event.target.classList.contains("filter-group__checkbox")) {
        page = 1;
        localStorage.setItem("currentPage", page);
        fetchBooks();
      }
    });

    $("#price-slider").on("slidechange", function () {
      page = 1;
      localStorage.setItem("currentPage", page);
      fetchBooks();
    });

    $(".filter-group__input").on("input change", function () {
      page = 1;
      localStorage.setItem("currentPage", page);
      fetchBooks();
    });

  });



  //   window.addEventListener("load", () => {
  //     const selectedCategory = localStorage.getItem('selectedCategory');
  //     const homeView = document.getElementById('home-view');
  //     const productListView = document.getElementById('product-list-view');

  //     // Nếu có một thể loại đã được chọn (và không phải là 'Tất cả') -> người dùng đang ở trang danh mục
  //     if (selectedCategory && selectedCategory !== 'home-view') {
  //       homeView.classList.add('hide-item');
  //       productListView.classList.remove('hide-item');

  //       page = parseInt(localStorage.getItem("currentPage")) || 1;
  //       pageSize = parseInt(localStorage.getItem("pageSize")) || 10;
  //       fetchBooks(); // Chỉ gọi fetchBooks khi cần
  //     } else {
  //       // Mặc định là trang chủ
  //       homeView.classList.remove('hide-item');
  //       productListView.classList.add('hide-item');
  //       loadHomePageContent(); // Chỉ tải nội dung trang chủ
  //     }
  //   });
  // });

  window.addEventListener("load", () => {
    const homeView = document.getElementById('home-view');
    const productListView = document.getElementById('product-list-view');

    // Luôn bắt đầu ở trang chủ khi tải trang
    // Xóa trạng thái cũ để đảm bảo sự trong sạch
    localStorage.removeItem('selectedCategory');
    localStorage.removeItem('currentPage');
    // Bạn cũng có thể xóa các bộ lọc khác nếu có
    // localStorage.removeItem('selectedAuthors'); 

    // Hiển thị view trang chủ và ẩn view danh sách sản phẩm
    homeView.classList.remove('hide-item');
    productListView.classList.add('hide-item');

    // Tải nội dung cho trang chủ
    loadHomePageContent();
  });
});

